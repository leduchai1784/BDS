import { NextResponse } from 'next/server'
import { auth } from '@/lib/auth'
import { createWorker } from 'tesseract.js'

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
    if (image.startsWith('data:image/')) {
      base64Data = image.substring(image.indexOf(',') + 1)
    }

    const buffer = Buffer.from(base64Data, 'base64')

    // Initialize Tesseract worker for Vietnamese language processing
    const worker = await createWorker('vie')
    const { data: { text } } = await worker.recognize(buffer)
    await worker.terminate()

    console.log(`--- Tesseract OCR Scanned Text (${side}) ---`)
    console.log(text)
    console.log('-------------------------------------------')

    const result: Record<string, string> = {}
    const lines = text.split('\n').map(l => l.trim()).filter(Boolean)

    if (side === 'front') {
      // 1. Extract 12 digit number
      const numMatch = text.match(/\b\d{12}\b/)
      if (numMatch) {
        result.number = numMatch[0]
      } else {
        // Fallback for character recognition errors (e.g. O/o instead of 0)
        const normalizedText = text.replace(/[oO]/g, '0')
        const genericNum = normalizedText.match(/\b\d{12}\b/)
        if (genericNum) result.number = genericNum[0]
      }

      // 2. Extract Date of birth (dob)
      const dobMatch = text.match(/(\d{2})[/-](\d{2})[/-](\d{4})/)
      if (dobMatch) {
        result.dob = `${dobMatch[3]}-${dobMatch[2]}-${dobMatch[1]}`
      }

      // 3. Extract Place of origin (pob / quê quán)
      let pobText = ''
      for (let i = 0; i < lines.length; i++) {
        if (/quê\s*quán|origin/i.test(lines[i])) {
          const parts = lines[i].split(/[:;]/)
          if (parts.length > 1 && parts[1].trim().length > 3) {
            pobText = parts[1].trim()
          } else if (i + 1 < lines.length) {
            pobText = lines[i + 1]
          }
          break
        }
      }
      if (pobText) result.pob = pobText.replace(/^[^a-zA-Z0-9À-ỹ]+/, '')

      // 4. Extract Permanent address (nơi thường trú)
      let addressText = ''
      for (let i = 0; i < lines.length; i++) {
        if (/thường\s*trú|residence/i.test(lines[i])) {
          const parts = lines[i].split(/[:;]/)
          if (parts.length > 1 && parts[1].trim().length > 3) {
            addressText = parts[1].trim()
          } else if (i + 1 < lines.length) {
            addressText = lines[i + 1]
            if (i + 2 < lines.length && !/quốc\s*tịch|hạn/i.test(lines[i + 2])) {
              addressText += ', ' + lines[i + 2]
            }
          }
          break
        }
      }
      if (addressText) result.permanent_address = addressText.replace(/^[^a-zA-Z0-9À-ỹ]+/, '')

    } else {
      // Back side
      // 1. Extract Issue date (ngày cấp)
      const dateMatch = text.match(/ngày\s*(\d{1,2})\s*tháng\s*(\d{1,2})\s*năm\s*(\d{4})/i)
      if (dateMatch) {
        result.issue_date = `${dateMatch[3]}-${dateMatch[2].padStart(2, '0')}-${dateMatch[1].padStart(2, '0')}`
      } else {
        const fallbackDate = text.match(/(\d{2})[/-](\d{2})[/-](\d{4})/)
        if (fallbackDate) {
          result.issue_date = `${fallbackDate[3]}-${fallbackDate[2]}-${fallbackDate[1]}`
        }
      }

      // 2. Extract Issue place (nơi cấp)
      let issuePlace = ''
      if (text.toLowerCase().includes('cục trưởng') || text.toLowerCase().includes('cảnh sát')) {
        issuePlace = 'Cục trưởng Cục Cảnh sát quản lý hành chính về trật tự xã hội'
      } else {
        for (const line of lines) {
          if (/cục|công\s*an/i.test(line)) {
            issuePlace = line
            break
          }
        }
      }
      if (issuePlace) result.issue_place = issuePlace
    }

    return NextResponse.json({
      success: true,
      data: result
    })
  } catch (error: any) {
    console.error('Scan CCCD Tesseract error:', error)
    return NextResponse.json({ 
      success: false, 
      message: `Đã xảy ra lỗi trong quá trình quét OCR bằng Tesseract: ${error.message}` 
    }, { status: 500 })
  }
}
