import { NextResponse } from 'next/server'

export const dynamic = 'force-dynamic'

export async function GET(request: Request) {
  try {
    const { searchParams } = new URL(request.url)
    const email = searchParams.get('email') || ''
    const phone = searchParams.get('phone') || ''

    if (!email && !phone) {
      return NextResponse.json({ success: true, data: [] })
    }

    process.env.NODE_TLS_REJECT_UNAUTHORIZED = '0'
    const response = await fetch('https://online.nks.vn/api/nks/rsitems', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({}),
      next: { revalidate: 30 }
    })

    if (!response.ok) {
      return NextResponse.json({ success: false, error: 'Failed to fetch NKS items' }, { status: 500 })
    }

    const data = await response.json()
    if (!data?.success || !Array.isArray(data.data)) {
      return NextResponse.json({ success: true, data: [] })
    }

    // Filter properties belonging to this agent
    const agentProperties = data.data.filter((item: any) => {
      const saleEmail = item.sale?.email?.toLowerCase() || ''
      const salePhone = item.sale?.phone || ''
      
      const matchEmail = email && saleEmail === email.toLowerCase()
      const matchPhone = phone && salePhone.replace(/\D/g, '') === phone.replace(/\D/g, '')

      return matchEmail || matchPhone
    }).map((item: any) => ({
      id: `nks-${item.id}`,
      title: item.title,
      address: item.address || 'Đồng Nai',
      priceLabel: item.formatedPrice || `${(item.price / 1000000000).toFixed(1)} tỷ`,
      area: item.total_area || 0,
      featureimg: item.featureimg || ''
    }))

    return NextResponse.json({ success: true, data: agentProperties })
  } catch (error: any) {
    console.error('Failed to get agent properties:', error)
    return NextResponse.json({ success: false, error: error.message }, { status: 500 })
  }
}
