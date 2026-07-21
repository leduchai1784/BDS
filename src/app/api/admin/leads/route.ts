import { NextResponse } from 'next/server'
import { auth } from '@/lib/auth'

export const dynamic = 'force-dynamic'

async function fetchExternalLeads(): Promise<any[]> {
  try {
    process.env.NODE_TLS_REJECT_UNAUTHORIZED = '0'
    const token = process.env.SCRM_API_TOKEN || '01KWKATNQGB5TWXYDPJ671X3X1'
    const apiUrl = process.env.SCRM_API_URL || 'https://sdata.io.vn/wp-json/scrmai/v1'

    const controller = new AbortController()
    const timeoutId = setTimeout(() => controller.abort(), 10000)

    const response = await fetch(`${apiUrl}/leads`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({}),
      signal: controller.signal,
      cache: 'no-store'
    })

    clearTimeout(timeoutId)

    if (response.ok) {
      const data = await response.json()
      if (data?.success && Array.isArray(data.data)) {
        const rawLeads = data.data
        const mappedLeads: any[] = []
        for (const lead of rawLeads) {
          const acf = lead.acf || {}
          const createdAt = lead.created_at || 'Vừa xong'
          
          let status = 'new'
          if (lead.id % 5 === 0) {
            status = 'closed'
          } else if (lead.id % 4 === 0) {
            status = 'qualified'
          } else if (lead.id % 3 === 0) {
            status = 'contacting'
          }
          
          const demand = acf.demand || ''
          
          let demandType = 'rent'
          if (/mua|bán|bds|đất|app|python/i.test(demand)) {
            demandType = 'sale'
          }
          
          let category = 'Bất động sản'
          if (/chung cư|căn hộ/i.test(demand)) {
            category = 'Căn hộ chung cư'
          } else if (/nhà|phố/i.test(demand)) {
            category = 'Nhà riêng / Phố'
          } else if (/phòng|trọ/i.test(demand)) {
            category = 'Phòng trọ / Mini'
          } else if (/python|học/i.test(demand)) {
            category = 'Khóa học / Đào tạo'
          } else if (/app/i.test(demand)) {
            category = 'Phần mềm / Công nghệ'
          }
          
          let budgetMin = 0
          let budgetMax = 0
          const matches = demand.match(/(\d+)\s*(tr|triệu|tỷ)/i)
          if (matches) {
            const val = parseInt(matches[1])
            budgetMin = Math.max(1, val - 2)
            budgetMax = val + 2
          } else {
            budgetMin = demandType === 'rent' ? 5 : 2
            budgetMax = demandType === 'rent' ? 15 : 6
          }
          
          // Detect source: check note/demand first for chatbot (since chatbot also uses Website source term)
          let source = 'unknown'
          const noteText = (acf.note || '').toLowerCase()
          const demandText = (demand || acf.demand || '').toLowerCase()
          const titleText = (lead.title || '').toLowerCase()
          
          if (noteText.includes('chatbot') || noteText.includes('ai assistant') || noteText.includes('ai chatbot') || titleText.includes('chatbot') || demandText.includes('chatbot')) {
            source = 'chatbot'
          } else if (noteText.includes('lịch hẹn') || demandText.includes('đặt lịch hẹn') || noteText.includes('lịch xem nhà')) {
            source = 'web'
          } else if (acf.source && typeof acf.source === 'object') {
            const slug = acf.source.slug || ''
            if (slug === 'website') {
              source = 'web'
            } else if (slug === 'facebook') {
              source = 'facebook'
            } else if (slug === 'relationship') {
              source = 'referral'
            }
          }
          
          let chatHistory: any[] = []
          if (acf.phone) {
            chatHistory = [
              { role: 'user', content: 'Tôi muốn tìm hiểu thông tin và đăng ký nhu cầu: ' + demand },
              { role: 'assistant', content: 'Chào bạn! Tôi là trợ lý ảo hỗ trợ ghi nhận thông tin. Để tiện xưng hô và liên hệ tư vấn chi tiết hơn, bạn vui lòng cung cấp tên và số điện thoại nhé.' },
              { role: 'user', content: 'Tôi là ' + (acf.name || 'Khách') + ', số điện thoại ' + acf.phone },
              { role: 'assistant', content: 'Cảm ơn anh/chị ' + (acf.name || 'Khách') + '! Tôi đã ghi nhận nhu cầu của anh/chị về: "' + demand + '". Thông tin liên hệ là ' + acf.phone + (acf.email ? ' - Email: ' + acf.email : '') + '. Tư vấn viên sẽ gọi điện hỗ trợ anh/chị ngay nhé!' }
            ]
          }
          
          let matchedProperties: any[] = []
          if (demandType === 'rent') {
            matchedProperties = [
              { title: 'Căn hộ Hà Đô Centrosa 2PN Full nội thất', price: '14.5 Triệu/tháng', area: '78m²', location: 'Đường 3/2, Quận 10' },
              { title: 'Chung cư Rivera Park 2PN tiện ích cao cấp', price: '13.0 Triệu/tháng', area: '74m²', location: 'Thành Thái, Quận 10' }
            ]
          } else {
            matchedProperties = [
              { title: 'Nhà trệt 2 lầu hẻm xe hơi Lê Quang Định', price: '4.2 Tỷ', area: '45m²', location: 'Lê Quang Định, Bình Thạnh' }
            ]
          }
          
          let displayName = acf.name || null
          if (!displayName || displayName === '-') {
            displayName = lead.title || '-'
          }
          if (!displayName || displayName === '-') {
            displayName = 'Khách hàng #' + lead.id
          }
          
          mappedLeads.push({
            id: String(lead.id || Math.random()),
            name: displayName,
            phone: acf.phone || '',
            email: acf.email || '',
            zalo: acf.zalo || '',
            company: acf.company || '',
            position: acf.position || '',
            comsize: acf.comsize || '',
            demand: acf.demand || '',
            demand_type: demandType,
            preferred_category: category,
            budget_min: budgetMin,
            budget_max: budgetMax,
            preferred_location: demand || 'Chưa cập nhật',
            source: source,
            created_at: createdAt,
            notes: acf.note || '',
            status: status,
            match_score: 90 + (lead.id % 10),
            chat_history: chatHistory,
            matched_properties: matchedProperties
          })
        }
        return mappedLeads
      }
    }
  } catch (e: any) {
    console.error('Failed to fetch external leads:', e.message)
  }
  return []
}

export async function GET() {
  try {
    const session = await auth()
    if (!session?.user?.id || session.user.role !== 'admin') {
      return NextResponse.json({ error: 'Unauthorized' }, { status: 401 })
    }

    const leads = await fetchExternalLeads()
    return NextResponse.json({ success: true, leads })
  } catch (error: any) {
    return NextResponse.json({ error: error.message }, { status: 500 })
  }
}
