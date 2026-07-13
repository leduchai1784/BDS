import { NextResponse } from 'next/server'
import { auth } from '@/lib/auth'

function formatOcrDate(dateStr: string): string {
  if (!dateStr) return ''
  const clean = dateStr.replace(/\s/g, '')
  const parts = clean.split('/')
  if (parts.length === 3) {
    const [day, month, year] = parts
    return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`
  }
  return dateStr
}

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
    
    // Create Blob from Buffer
    const blob = new Blob([buffer], { type: 'image/jpeg' })
    const formData = new FormData()
    formData.append('image', blob, 'cccd.jpg')

    const apiKey = process.env.FPT_AI_API_KEY || 'jEg5yvUc8HLoUnesjGKVuBEyaZz1NRFa'

    const response = await fetch('https://api.fpt.ai/vision/idr/vnm', {
      method: 'POST',
      headers: {
        'api-key': apiKey
      },
      body: formData
    })

    if (!response.ok) {
      return NextResponse.json({ 
        success: false, 
        message: 'Không thể kết nối đến API OCR của FPT.' 
      }, { status: 500 })
    }

    const ocrData = await response.json()
    if (ocrData.errorCode !== undefined && ocrData.errorCode !== 0 && ocrData.errorCode !== '0') {
      return NextResponse.json({ 
        success: false, 
        message: `Lỗi OCR từ FPT: ${ocrData.errorMessage || 'Không xác định'}` 
      }, { status: 422 })
    }

    const data = ocrData.data?.[0]
    if (!data) {
      return NextResponse.json({ 
        success: false, 
        message: 'Không thể nhận dạng hình ảnh.' 
      }, { status: 422 })
    }

    const result: Record<string, string> = {}
    if (side === 'front') {
      if (data.id) result.number = data.id
      if (data.dob) result.dob = formatOcrDate(data.dob)
      if (data.home) result.pob = data.home
      if (data.address) result.permanent_address = data.address
    } else {
      if (data.issue_date) result.issue_date = formatOcrDate(data.issue_date)
      if (data.issue_loc) result.issue_place = data.issue_loc
      if (data.address) result.permanent_address = data.address
    }

    return NextResponse.json({
      success: true,
      data: result
    })
  } catch (error: any) {
    console.error('Scan CCCD error:', error)
    return NextResponse.json({ 
      success: false, 
      message: `Đã xảy ra lỗi trong quá trình quét OCR: ${error.message}` 
    }, { status: 500 })
  }
}
