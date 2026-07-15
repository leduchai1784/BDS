import { NextResponse } from 'next/server'
import { auth } from '@/lib/auth'
import { prisma } from '@/lib/prisma'

export async function DELETE(
  req: Request,
  { params }: { params: Promise<{ id: string }> }
) {
  try {
    const session = await auth()
    if (!session?.user?.id) {
      return NextResponse.json({ error: 'Unauthorized' }, { status: 401 })
    }

    const userId = Number(session.user.id)
    const resolvedParams = await params
    const campaignId = resolvedParams.id

    const campaign = await prisma.aiCampaign.findUnique({
      where: { id: campaignId }
    })

    if (!campaign) {
      return NextResponse.json({ error: 'Campaign not found' }, { status: 404 })
    }

    if (Number(campaign.ownerId) !== userId) {
      return NextResponse.json({ error: 'Permission denied' }, { status: 403 })
    }

    await prisma.aiCampaign.delete({
      where: { id: campaignId }
    })

    return NextResponse.json({
      success: true,
      message: 'Đã xóa chiến dịch thành công.'
    })
  } catch (error: any) {
    console.error('Delete campaign error:', error)
    return NextResponse.json({ error: error.message }, { status: 500 })
  }
}
