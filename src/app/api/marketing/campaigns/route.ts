import { NextResponse } from 'next/server'
import { auth } from '@/lib/auth'
import { prisma } from '@/lib/prisma'

function getReadableGoal(goal: string): string {
  return {
    rent_fast: 'Cho thuê nhanh',
    luxury_brand: 'Quảng bá thương hiệu cao cấp',
    price_deal: 'Cắt lỗ gấp / Ưu đãi tốt',
    review_detail: 'Review chi tiết trải nghiệm',
  }[goal] || 'Quảng cáo tổng hợp'
}

function getReadableTone(tone: string): string {
  return {
    friendly: 'Thân thiện, gần gũi',
    professional: 'Chuyên nghiệp, tin cậy',
    funny: 'Hài hước, bắt trend',
    emotional: 'Gợi mở cảm xúc',
  }[tone] || 'Tự nhiên'
}

// GET /api/marketing/campaigns - Fetch campaign history
export async function GET() {
  try {
    const session = await auth()
    if (!session?.user?.id) {
      return NextResponse.json({ error: 'Unauthorized' }, { status: 401 })
    }

    const userId = Number(session.user.id)

    const campaigns = await prisma.aiCampaign.findMany({
      where: { ownerId: userId },
      orderBy: { createdAt: 'desc' }
    })

    return NextResponse.json({
      success: true,
      campaigns
    })
  } catch (error: any) {
    console.error('Fetch history error:', error)
    return NextResponse.json({ error: error.message }, { status: 500 })
  }
}

// POST /api/marketing/campaigns - Save a campaign
export async function POST(req: Request) {
  try {
    const session = await auth()
    if (!session?.user?.id) {
      return NextResponse.json({ error: 'Unauthorized' }, { status: 401 })
    }

    const userId = Number(session.user.id)
    const body = await req.json()
    const { type, property_id, title, goal, tone, content } = body

    if (!type || !title || !tone || !content) {
      return NextResponse.json({ error: 'Missing required parameters' }, { status: 400 })
    }

    // Resolve property_id if it's a valid UUID
    let dbPropertyId: string | null = null
    if (property_id && property_id.match(/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i)) {
      dbPropertyId = property_id
    }

    const campaign = await prisma.aiCampaign.create({
      data: {
        ownerId: userId,
        propertyId: dbPropertyId,
        type,
        title,
        goal: goal ? getReadableGoal(goal) : null,
        tone: getReadableTone(tone),
        content
      }
    })

    return NextResponse.json({
      success: true,
      message: 'Lưu chiến dịch thành công.',
      campaign
    })
  } catch (error: any) {
    console.error('Save campaign error:', error)
    return NextResponse.json({ error: error.message }, { status: 500 })
  }
}
