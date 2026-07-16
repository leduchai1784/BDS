import { NextResponse } from 'next/server'
import { auth } from '@/lib/auth'
import { prisma } from '@/lib/prisma'
import { getNksProperties } from '@/lib/nks'
import { GoogleGenerativeAI } from '@google/generative-ai'

export const dynamic = 'force-dynamic'

async function syncChatbotLeadToCrm(message: string, replyText: string, history: any[]) {
  try {
    // Support spaces, dots, hyphens, country code +84, 84, or standard 0
    const phoneRegex = /(?:\+?84|0)(?:\s*[\d.-]){9,10}\b/
    const emailRegex = /[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/

    const phoneMatch = message.match(phoneRegex) || replyText.match(phoneRegex)
    if (!phoneMatch) return

    // Clean and normalize phone number
    let phone = phoneMatch[0].replace(/[\s.-]/g, '')
    if (phone.startsWith('+84')) {
      phone = '0' + phone.substring(3)
    } else if (phone.startsWith('84')) {
      phone = '0' + phone.substring(2)
    }
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

    // Build system instruction context (Customized AI Sales Prompts)
    const systemInstruction = `Bạn là AI Sales của CRM Bất động sản BDS Rental.

Dưới đây là danh sách bất động sản hiện có trong hệ thống (dữ liệu CRM):
${JSON.stringify(combinedProperties)}

Nhiệm vụ của bạn:
1. Tư vấn dự án: Giải đáp đầy đủ, chính xác thông tin về các dự án bất động sản có trong danh sách.
2. Giới thiệu sản phẩm: Giới thiệu chi tiết sản phẩm bất động sản phù hợp với nhu cầu của khách hàng (giá cả, diện tích, vị trí).
3. So sánh dự án: So sánh các bất động sản hoặc dự án về vị trí, giá bán/thuê, diện tích để giúp khách hàng dễ dàng đưa ra quyết định lựa chọn.
4. Tính khoản vay: Hỗ trợ tính toán lãi suất, số tiền trả góp gốc + lãi hàng tháng cho khách hàng dựa trên các thông số cơ bản (Ví dụ: tính khoản trả góp hàng tháng dựa trên thời hạn vay và lãi suất thực tế).
5. Hướng dẫn đặt lịch xem nhà: Hướng dẫn khách hàng chọn ngày, giờ và bấm vào nút đặt lịch xem nhà trực quan của tin đăng trên website.

Quy tắc bắt buộc:
- Tuyệt đối không tự bịa giá, không bịa thông tin bất động sản ngoài danh sách được cung cấp.
- Chỉ trả lời dựa trên dữ liệu CRM được cung cấp phía trên.
- Nếu thiếu dữ liệu hoặc không có trong danh sách thì trả lời một cách khéo léo là chưa có thông tin.
- Luôn trả lời lịch sự, thân thiện, tự nhiên bằng tiếng Việt.
- Khéo léo hỏi xin thông tin liên hệ của khách hàng (như Tên và Số điện thoại hoặc Zalo) trong quá trình tư vấn để lưu lại nhu cầu chăm sóc khách hàng tiềm năng (Leads).
- Luôn kết thúc phản hồi bằng một lời mời đặt lịch xem nhà hoặc lời mời để lại thông tin liên hệ để được chuyên viên tư vấn hỗ trợ trực tiếp.

Khuyến nghị bất động sản:
- Chọn lọc và gợi ý các bất động sản phù hợp nhất từ danh sách trên (tối đa 3 BĐS).
- Ở cuối phản hồi, bạn bắt buộc phải đính kèm danh sách các ID của những bất động sản mà bạn gợi ý cho khách hàng trong thẻ XML sau: <recommendations>[ID1, ID2, ...]</recommendations>.
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
