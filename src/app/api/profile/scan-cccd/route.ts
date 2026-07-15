import { NextResponse } from 'next/server'
import { auth } from '@/lib/auth'
import { GoogleGenerativeAI } from '@google/generative-ai'

export async function POST(req: Request) {
  try {
    const session = await auth()
    if (!session?.user?.id) {
      return NextResponse.json({ error: 'Unauthorized' }, { status: 401 })
    }

    const { image, side } = await req.json()

    if (!image || !side) {
      return NextResponse.json({ error: 'Missing image or side parameter' }, { status: 400 })
    }

    // Clean base64 prefix
    let base64Data = image
    let mimeType = 'image/jpeg'
    if (image.startsWith('data:image/')) {
      const commaIdx = image.indexOf(',')
      const prefix = image.substring(0, commaIdx)
      const mimeMatch = prefix.match(/data:(image\/\w+);base64/)
      if (mimeMatch) {
        mimeType = mimeMatch[1]
      }
      base64Data = image.substring(commaIdx + 1)
    }

    const apiKey = process.env.GEMINI_API_KEY
    if (!apiKey) {
      // Return fallback signal so client-side Tesseract runs
      return NextResponse.json({ 
        success: false, 
        fallback: true,
        message: 'Gemini API key is not configured. Falling back to local OCR.' 
      })
    }

    // Initialize Gemini AI
    const genAI = new GoogleGenerativeAI(apiKey)
    const modelName = process.env.GEMINI_MODEL || 'gemini-1.5-flash'
    const model = genAI.getGenerativeModel({ model: modelName })

    let prompt = ''
    if (side === 'front') {
      prompt = `Bạn là hệ thống OCR trích xuất thông tin từ ảnh mặt trước Căn cước công dân hoặc Chứng minh nhân dân Việt Nam.
Hãy đọc ảnh và trả về thông tin dưới dạng JSON theo cấu trúc sau:
{
  "number": "Số CCCD / CMND (12 số hoặc 9 số)",
  "dob": "Ngày sinh dạng YYYY-MM-DD",
  "pob": "Quê quán / Nơi sinh",
  "permanent_address": "Nơi thường trú",
  "gender": 1 hoặc 2 (1 là Nam, 2 là Nữ)
}
Chỉ trả về JSON hợp lệ, không thêm bất kỳ văn bản giải thích nào ngoài JSON.`
    } else {
      prompt = `Bạn là hệ thống OCR trích xuất thông tin từ ảnh mặt sau Căn cước công dân hoặc Chứng minh nhân dân Việt Nam.
Hãy đọc ảnh và trả về thông tin dưới dạng JSON theo cấu trúc sau:
{
  "issue_date": "Ngày cấp dạng YYYY-MM-DD",
  "issue_place": "Nơi cấp. Lưu ý quan trọng: Nếu nơi cấp là Cục Cảnh sát quản lý hành chính về trật tự xã hội hoặc bất kỳ cụm từ nào tương tự liên quan đến Cục trưởng Cục Cảnh sát, bạn phải trả về chính xác chuỗi: 'Cục Cảnh sát QLHC về TTXH'. Các trường hợp công an tỉnh/thành phố khác thì giữ nguyên (ví dụ: 'Công an tỉnh Hải Dương' hoặc 'Công an TP. Hà Nội')."
}
Chỉ trả về JSON hợp lệ, không thêm bất kỳ văn bản giải thích nào ngoài JSON.`
    }

    const imagePart = {
      inlineData: {
        data: base64Data,
        mimeType
      }
    }

    const result = await model.generateContent([prompt, imagePart])
    const rawText = result.response.text()

    console.log(`--- Gemini OCR Raw Reply (${side}) ---`)
    console.log(rawText)
    console.log('------------------------------------')

    // Clean Markdown block markup if Gemini returned it
    let cleanJsonStr = rawText.trim()
    if (cleanJsonStr.startsWith('```')) {
      cleanJsonStr = cleanJsonStr.replace(/^```json\s*/, '').replace(/```$/, '').trim()
    }

    const parsedData = JSON.parse(cleanJsonStr)

    return NextResponse.json({
      success: true,
      data: parsedData
    })

  } catch (error: any) {
    console.error('Gemini OCR API error:', error)
    // Send fallback signal to let client-side Tesseract.js try
    return NextResponse.json({ 
      success: false, 
      fallback: true,
      message: `Gemini OCR error: ${error.message}. Falling back to local OCR.` 
    })
  }
}
