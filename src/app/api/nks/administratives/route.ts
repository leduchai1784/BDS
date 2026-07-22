import { NextResponse } from 'next/server'
import axios from 'axios'
import https from 'https'

const httpsAgent = new https.Agent({
  rejectUnauthorized: false
})

export async function POST(req: Request) {
  try {
    const { searchParams } = new URL(req.url)
    const provinceId = searchParams.get('province_id') || '75'

    const response = await axios.post(`https://online.nks.vn/api/nks/administratives?province_id=${provinceId}&slcBox=true`, {}, {
      timeout: 10000,
      httpsAgent
    })
    return NextResponse.json(response.data)
  } catch (error: any) {
    console.error('Lỗi proxy NKS administratives API:', error.message)
    return NextResponse.json({ success: false, data: [] }, { status: 500 })
  }
}
