import { NextResponse } from 'next/server'
import { getNksWardsByProvince } from '@/lib/nks'

export const dynamic = 'force-dynamic'

export async function POST(req: Request) {
  try {
    const { searchParams } = new URL(req.url)
    const provinceId = searchParams.get('province_id') || '75'

    const data = await getNksWardsByProvince(Number(provinceId))
    return NextResponse.json({ success: true, data })
  } catch (error: any) {
    console.error('Lỗi proxy NKS administratives API:', error.message)
    return NextResponse.json({ success: false, data: [] })
  }
}
