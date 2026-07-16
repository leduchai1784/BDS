import { NextResponse } from 'next/server'
import { auth } from '@/lib/auth'
import { prisma } from '@/lib/prisma'
import { getNksProperties } from '@/lib/nks'
import { GoogleGenerativeAI } from '@google/generative-ai'

export const dynamic = 'force-dynamic'

async function syncChatbotLeadToCrm(message: string, replyText: string, history: any[]) {
  try {
    const phoneRegex = /(0[3|5|7|8|9][0-9]{8})\b/
    const emailRegex = /[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/

    const phoneMatch = message.match(phoneRegex) || replyText.match(phoneRegex)
    if (!phoneMatch) return

    const phone = phoneMatch[1]
    const emailMatch = message.match(emailRegex) || replyText.match(emailRegex)
    const email = emailMatch ? emailMatch[0] : ''

    // Try to find a name from history or current message
    let name = 'Khách hàng Chatbot'
    const nameMatch = message.match(/(?:tên là|tôi tên|là|tên tôi là)\s*([A-ZÀ-Ỹa-zà-ỹ\s]{2,30})/i)
    if (nameMatch) {
      name = nameMatch[1].trim()
    } else if (history && Array.isArray(history)) {
      for (const turn of history) {
        const hMatch = turn.content?.match(/(?:tên là|tôi tên|là|tên tôi là)\s*([A-ZÀ-Ỹa-zà-ỹ\s]{2,30})/i)
        if (hMatch) {
          name = hMatch[1].trim()
          break
        }
      }
    }

    // Try to find the demand
    let demand = message
    if (history && Array.isArray(history) && history.length > 0) {
      demand = history.map((h: any) => h.role === 'user' ? h.content : '').filter(Boolean).join(' | ').slice(-200)
    }

    process.env.NODE_TLS_REJECT_UNAUTHORIZED = '0'
    const token = process.env.SCRM_API_TOKEN || '01KWKATNQGB5TWXYDPJ671X3X1'
    const apiUrl = process.env.SCRM_API_URL || 'https://sdata.io.vn/wp-json/scrmai/v1'

    if (true) {
      await fetch(`${apiUrl}/lead/create`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          title: `${name} - ${phone}`,
          acf: {
            name: name,
            phone: phone,
            email: email,
            zalo: phone,
            demand: demand,
            source: {
              slug: 'chatbot',
              name: 'AI Chatbot'
            },
            note: 'Khách hàng tiềm năng từ cuộc gọi/chat với AI Assistant'
          }
        })
      })
    }
  } catch (err) {
    console.error('Failed to sync chatbot lead to SCRM:', err)
  }
}

// Simple in-memory IP rate limiter
const rateLimitMap = new Map<string, { count: number; resetTime: number }>()

function checkRateLimit(ip: string): { limited: boolean; remainingSeconds: number } {
  const now = Date.now()
  const limit = 30
  const windowMs = 60 * 1000 // 1 minute

  const record = rateLimitMap.get(ip)
  if (!record) {
    rateLimitMap.set(ip, { count: 1, resetTime: now + windowMs })
    return { limited: false, remainingSeconds: 0 }
  }

  if (now > record.resetTime) {
    rateLimitMap.set(ip, { count: 1, resetTime: now + windowMs })
    return { limited: false, remainingSeconds: 0 }
  }

  if (record.count >= limit) {
    const remainingSeconds = Math.max(1, Math.round((record.resetTime - now) / 1000))
    return { limited: true, remainingSeconds }
  }

  record.count++
  return { limited: false, remainingSeconds: 0 }
}

export async function POST(req: Request) {
  try {
    const session = await auth()
    const ip = req.headers.get('x-forwarded-for') || session?.user?.id || '127.0.0.1'

    // 1. Rate Limiting Check
    const rateLimit = checkRateLimit(ip)
    if (rateLimit.limited) {
      return NextResponse.json({
        success: false,
        reply: `Bạn đã gửi quá nhiều tin nhắn. Vui lòng thử lại sau ${rateLimit.remainingSeconds} giây.`
      }, { status: 429 })
    }

    // 2. Parse request body
    const body = await req.json()
    const { message, history } = body

    if (!message || typeof message !== 'string') {
      return NextResponse.json({ success: false, reply: 'Message is required' }, { status: 400 })
    }

    if (message.length > 500) {
      return NextResponse.json({ success: false, reply: 'Tin nhắn quá dài (tối đa 500 ký tự).' }, { status: 400 })
    }

    const apiKey = process.env.GEMINI_API_KEY || ''
    const modelName = process.env.GEMINI_MODEL || 'gemini-3.1-flash-lite'

    if (!apiKey) {
      return NextResponse.json({
        success: true,
        reply: 'Hệ thống chưa cấu hình API Key cho Chatbot AI. Vui lòng liên hệ quản trị viên.',
        properties: []
      })
    }

    // 3. Load properties lists for AI Context
    const [dbProps, nksPropsRaw] = await Promise.all([
      prisma.property.findMany({
        where: { status: 'approved', deletedAt: null },
        orderBy: { createdAt: 'desc' },
        take: 15
      }),
      getNksProperties().catch(() => [])
    ])

    // Map db properties to standard objects matching NKS structure
    const mappedDbProps = dbProps.map(p => ({
      id: p.id,
      title: p.title,
      type: p.propertyType || 'Chung cư',
      price: p.priceLabel,
      area: p.area + 'm2',
      location: p.address,
      district: p.district,
      image: '' // Local DB image mapping details
    }))

    const mappedNksProps = nksPropsRaw.map((p: any) => ({
      id: String(p.id),
      title: p.title,
      type: p.type || 'Chung cư',
      price: p.price,
      area: p.area + 'm2',
      location: p.location,
      district: p.district || '',
      image: p.image || ''
    }))

    const combinedProperties = [...mappedDbProps, ...mappedNksProps].slice(0, 30)

    // Build system instruction context
    const systemInstruction = `Bạn là Trợ lý ảo AI của BDS Rental, chuyên tư vấn và gợi ý bất động sản cho thuê tại Việt Nam.
Hãy trả lời một cách tự nhiên, lịch sự, thân thiện bằng tiếng Việt và hỗ trợ khách hàng tìm kiếm bất động sản phù hợp.

Dưới đây là danh sách bất động sản hiện có trong hệ thống (dữ liệu từ hệ thống BDS Rental):
${JSON.stringify(combinedProperties)}

Nhiệm vụ của bạn:
1. Trả lời câu hỏi của người dùng và tư vấn dựa trên nhu cầu của họ (khu vực, giá cả, loại hình, diện tích).
2. Chọn lọc và gợi ý các bất động sản phù hợp nhất từ danh sách trên (tối đa 3 BĐS).
3. Ở cuối phản hồi, bạn bắt buộc phải đính kèm danh sách các ID của những bất động sản mà bạn gợi ý cho khách hàng trong thẻ XML sau: <recommendations>[ID1, ID2, ...]</recommendations>.
Ví dụ: Nếu gợi ý căn hộ có ID 123 và 456, hãy viết ở cuối phản hồi là: <recommendations>[123, 456]</recommendations>
Nếu không tìm thấy bất động sản nào phù hợp, hãy trả lời lịch sự và để trống recommendations: <recommendations>[]</recommendations>`

    // 4. Setup Gemini generative model
    const genAI = new GoogleGenerativeAI(apiKey)
    const model = genAI.getGenerativeModel({
      model: modelName,
      systemInstruction
    })

    // 5. Format conversation history
    let geminiHistory: any[] = []
    if (history && Array.isArray(history)) {
      for (const turn of history.slice(-10)) { // limit history length
        geminiHistory.push({
          role: turn.role === 'user' ? 'user' : 'model',
          parts: [{ text: turn.content }]
        })
      }
    }

    // Filter out leading model messages to comply with Gemini SDK requirement (first message must be 'user')
    const firstUserIdx = geminiHistory.findIndex(h => h.role === 'user')
    if (firstUserIdx !== -1) {
      geminiHistory = geminiHistory.slice(firstUserIdx)
    } else {
      geminiHistory = []
    }

    geminiHistory.push({
      role: 'user',
      parts: [{ text: message }]
    })

    // 6. Generate reply content
    const chatSession = model.startChat({
      history: geminiHistory.slice(0, -1),
      generationConfig: {
        temperature: 0.3,
        topP: 0.95,
        maxOutputTokens: 1024
      }
    })

    const result = await chatSession.sendMessage(message)
    let replyText = result.response.text().trim()

    // 7. Extract XML recommended IDs from reply
    let recommendedIds: string[] = []
    const xmlRegex = /<recommendations>\[([\s\S]*?)\]<\/recommendations>/
    const matches = replyText.match(xmlRegex)
    if (matches) {
      const idsString = matches[1]
      if (idsString.trim()) {
        recommendedIds = idsString.split(',').map(s => s.trim().replace(/['"]/g, ''))
      }
      // Remove XML block for clean user interface rendering
      replyText = replyText.replace(xmlRegex, '').trim()
    }

    // 8. Find details of recommended properties to display below reply bubble
    const recommendedProperties = []
    if (recommendedIds.length > 0) {
      for (const prop of combinedProperties) {
        if (recommendedIds.includes(prop.id)) {
          recommendedProperties.push(prop)
        }
      }
    }

    // Push lead to SCRM CRM if phone number exists in conversation
    await syncChatbotLeadToCrm(message, replyText, history)

    return NextResponse.json({
      success: true,
      reply: replyText,
      properties: recommendedProperties.slice(0, 3)
    })
  } catch (error: any) {
    console.error('Chatbot API Error:', error)
    return NextResponse.json({
      success: false,
      reply: 'Xin lỗi, đã xảy ra lỗi khi xử lý tin nhắn. Vui lòng thử lại sau.'
    }, { status: 500 })
  }
}
