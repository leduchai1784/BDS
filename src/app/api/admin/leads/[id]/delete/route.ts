import { NextResponse } from 'next/server'
import { auth } from '@/lib/auth'

export async function POST(
  req: Request,
  { params }: { params: Promise<{ id: string }> }
) {
  try {
    const session = await auth()
    if (!session?.user?.id || session.user.role !== 'admin') {
      return NextResponse.json({ error: 'Unauthorized' }, { status: 401 })
    }

    const resolvedParams = await params
    const leadId = Number(resolvedParams.id)

    const token = process.env.SCRM_API_TOKEN
    const apiUrl = process.env.SCRM_API_URL || 'https://sdata.io.vn/wp-json/scrmai/v1'

    if (!token) {
      return NextResponse.json({ error: 'SCRM Token not configured' }, { status: 500 })
    }

    const response = await fetch(`${apiUrl}/lead/delete`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        id: leadId
      })
    })

    if (!response.ok) {
      const errorText = await response.text()
      return NextResponse.json({ error: `SCRM API Error: ${errorText}` }, { status: response.status })
    }

    const result = await response.json()
    if (result && result.success) {
      return NextResponse.json({ success: true, message: 'Xoá Lead thành công!' })
    } else {
      return NextResponse.json({ error: 'SCRM API returned failure' }, { status: 400 })
    }
  } catch (error: any) {
    console.error('Delete lead CRM error:', error)
    return NextResponse.json({ error: error.message }, { status: 500 })
  }
}
