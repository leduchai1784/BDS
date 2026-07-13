import { NextResponse } from 'next/server'
import { auth } from '@/lib/auth'
import { prisma } from '@/lib/prisma'

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
    const appointmentId = BigInt(resolvedParams.id)

    const appointment = await prisma.appointment.findUnique({
      where: { id: appointmentId }
    })

    if (!appointment) {
      return NextResponse.json({ error: 'Lịch hẹn không tồn tại' }, { status: 404 })
    }

    await prisma.appointment.update({
      where: { id: appointmentId },
      data: { status: 'cancelled' }
    })

    return NextResponse.json({
      success: true,
      message: 'Đã hủy lịch hẹn xem nhà thành công!'
    })
  } catch (error: any) {
    console.error('Cancel appointment admin API error:', error)
    return NextResponse.json({ error: error.message }, { status: 500 })
  }
}
