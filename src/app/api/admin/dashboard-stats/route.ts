import { NextResponse } from 'next/server'
import { auth } from '@/lib/auth'
import { prisma } from '@/lib/prisma'

export const dynamic = 'force-dynamic'

async function fetchExternalLeadsCount(): Promise<number> {
  try {
    process.env.NODE_TLS_REJECT_UNAUTHORIZED = '0'
    const token = process.env.SCRM_API_TOKEN || '01KWKATNQGB5TWXYDPJ671X3X1'
    const apiUrl = process.env.SCRM_API_URL || 'https://sdata.io.vn/wp-json/scrmai/v1'

    const controller = new AbortController()
    const timeoutId = setTimeout(() => controller.abort(), 4000)

    const response = await fetch(`${apiUrl}/leads`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      },
      signal: controller.signal,
      cache: 'no-store'
    })

    clearTimeout(timeoutId)

    if (response.ok) {
      const data = await response.json()
      if (data?.success && Array.isArray(data.data)) {
        return data.data.length
      }
    }
    return 0
  } catch (e: any) {
    console.error('Failed to fetch external leads count:', e.message)
    return 0
  }
}

export async function GET() {
  const session = await auth()

  if (!session?.user?.id || session.user.role !== 'admin') {
    return NextResponse.json({ error: 'Unauthorized' }, { status: 401 })
  }

  try {
    const userCount = await prisma.user.count()
    const propertyCount = await prisma.property.count()
    const appointmentCount = await prisma.appointment.count()
    const leadCount = await fetchExternalLeadsCount()

    // Fetch external agents to sum into users count
    let nksAgentsCount = 0
    try {
      const response = await fetch('https://online.nks.vn/api/nks/rsagents', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({}),
        next: { revalidate: 30 }
      })
      if (response.ok) {
        const data = await response.json()
        if (data?.success && Array.isArray(data.data)) {
          nksAgentsCount = data.data.length
        }
      }
    } catch (e) {
      console.error('Failed to get nks agents count:', e)
    }

    return NextResponse.json({
      success: true,
      stats: {
        users: userCount + nksAgentsCount,
        properties: propertyCount,
        appointments: appointmentCount,
        leads: leadCount
      }
    })
  } catch (error: any) {
    console.error('Dashboard stats api error:', error)
    return NextResponse.json({ success: false, error: error.message }, { status: 500 })
  }
}
