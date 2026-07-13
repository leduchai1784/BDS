import { NextResponse } from 'next/server'
import { auth } from '@/lib/auth'
import { prisma } from '@/lib/prisma'
import { getNksProperties } from '@/lib/nks'
import { GoogleGenerativeAI } from '@google/generative-ai'

export const dynamic = 'force-dynamic'

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
    const geminiHistory = []
    if (history && Array.isArray(history)) {
      for (const turn of history.slice(-10)) { // limit history length
        geminiHistory.push({
          role: turn.role === 'user' ? 'user' : 'model',
          parts: [{ text: turn.content }]
        })
      }
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
    const xmlRegex = /<recommendations>\[(.*?)\]<\/recommendations>/s
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
