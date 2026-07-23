import { NextResponse } from 'next/server'
import { getNksProvinces } from '@/lib/nks'

export const dynamic = 'force-dynamic'

export async function POST() {
  try {
    const data = await getNksProvinces()
    return NextResponse.json({ success: true, data })
  } catch (error: any) {
    console.error('Lỗi proxy NKS provinces API:', error.message)
    return NextResponse.json({ success: false, data: [] })
  }
}
