import { NextResponse } from 'next/server'
import axios from 'axios'
import https from 'https'

const httpsAgent = new https.Agent({
  rejectUnauthorized: false
})

export async function POST(req: Request) {
  try {
    const response = await axios.post('https://online.nks.vn/api/nks/provinces?country_id=192&slcBox=true', {}, {
      timeout: 10000,
      httpsAgent
    })
    return NextResponse.json(response.data)
  } catch (error: any) {
    console.error('Lỗi proxy NKS provinces API:', error.message)
    return NextResponse.json({ success: false, data: [] }, { status: 500 })
  }
}
