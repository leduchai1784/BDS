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
    const { acf } = await req.json()

    let token = process.env.SCRM_API_TOKEN
    if (!token || token.trim() === '' || token === 'undefined' || token === 'null' || token.length < 10) {
      token = '01KWKATNQGB5TWXYDPJ671X3X1'
    }

    let apiUrl = process.env.SCRM_API_URL
    if (!apiUrl || apiUrl.trim() === '' || apiUrl === 'undefined' || apiUrl === 'null' || !apiUrl.startsWith('http')) {
      apiUrl = 'https://sdata.io.vn/wp-json/scrmai/v1'
    }

    const response = await fetch(`${apiUrl}/lead/update`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        id: leadId,
        acf: acf
      })
    })

    if (!response.ok) {
      const errorText = await response.text()
      return NextResponse.json({ error: `SCRM API Error: ${errorText}` }, { status: response.status })
    }

    const result = await response.json()
    if (result && result.success) {
      return NextResponse.json({ success: true, message: 'Cập nhật Lead thành công!' })
    } else {
      return NextResponse.json({ error: 'SCRM API returned failure' }, { status: 400 })
    }
  } catch (error: any) {
    console.error('Update lead CRM error:', error)
    return NextResponse.json({ error: error.message }, { status: 500 })
  }
}
