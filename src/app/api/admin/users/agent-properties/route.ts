import { NextResponse } from 'next/server'

export const dynamic = 'force-dynamic'

export async function GET(request: Request) {
  try {
    const { searchParams } = new URL(request.url)
    const email = searchParams.get('email') || ''
    const phone = searchParams.get('phone') || ''
    const accessToken = searchParams.get('access_token') || ''

    process.env.NODE_TLS_REJECT_UNAUTHORIZED = '0'

    // Case 1: If access_token is provided, use the user authenticated endpoint
    if (accessToken) {
      try {
        const userItemsRes = await fetch('https://account.nks.vn/api/nks/user/rsitems', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ access_token: accessToken }),
          next: { revalidate: 0 }
        })

        if (userItemsRes.ok) {
          const userItemsData = await userItemsRes.json()
          const itemsList = userItemsData?.data || userItemsData?.items || (Array.isArray(userItemsData) ? userItemsData : [])
          
          if (Array.isArray(itemsList) && itemsList.length > 0) {
            const formatted = itemsList.map((item: any) => ({
              id: `nks-${item.id}`,
              title: item.title,
              address: item.address || 'Đồng Nai',
              priceLabel: item.formatedPrice || `${(item.price / 1000000000).toFixed(1)} tỷ`,
              area: item.total_area || 0,
              featureimg: item.featureimg || ''
            }))
            return NextResponse.json({ success: true, data: formatted, source: 'authenticated_api' })
          }
        }
      } catch (tokenErr) {
        console.warn('Failed to fetch from account.nks.vn user items API, falling back to public items filter:', tokenErr)
      }
    }

    // Case 2: Fallback to filtering public rsitems by agent email/phone
    if (!email && !phone) {
      return NextResponse.json({ success: true, data: [] })
    }

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

    const agentId = searchParams.get('id') || searchParams.get('agent_id') || ''

    // Filter properties belonging to this agent
    const seenIds = new Set<string>()
    const agentProperties = data.data.filter((item: any) => {
      const saleId = item.sale?.id?.toString() || item.user_id?.toString() || ''
      const saleEmail = item.sale?.email?.toLowerCase() || ''
      
      const matchId = agentId && saleId === agentId.toString()
      const matchEmail = email && saleEmail === email.toLowerCase()

      return matchId || matchEmail
    }).filter((item: any) => {
      if (seenIds.has(item.id.toString())) return false
      seenIds.add(item.id.toString())
      return true
    }).map((item: any) => ({
      id: `nks-${item.id}`,
      title: item.title,
      address: item.address || 'Đồng Nai',
      priceLabel: item.formatedPrice || `${(item.price / 1000000000).toFixed(1)} tỷ`,
      area: item.total_area || 0,
      featureimg: item.featureimg || ''
    }))

    return NextResponse.json({ success: true, data: agentProperties, source: 'public_filtered_api' })
  } catch (error: any) {
    console.error('Failed to get agent properties:', error)
    return NextResponse.json({ success: false, error: error.message }, { status: 500 })
  }
}
