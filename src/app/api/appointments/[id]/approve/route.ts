import { NextResponse } from 'next/server'
import { auth } from '@/lib/auth'
import { prisma } from '@/lib/prisma'
import { sendEmail, getTenantApprovalHtml } from '@/lib/mail'

export async function POST(
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
    const appointmentId = Number(resolvedParams.id)

    if (isNaN(appointmentId)) {
      return NextResponse.json({ error: 'Invalid appointment ID' }, { status: 400 })
    }

    const appointment = await prisma.appointment.findUnique({
      where: { id: appointmentId }
    })

    if (!appointment) {
      return NextResponse.json({ error: 'Appointment not found' }, { status: 404 })
    }

    // Query property and owner separately
    const property = await prisma.property.findUnique({
      where: { id: appointment.propertyId },
      include: {
        owner: true
      }
    })

    if (!property) {
      return NextResponse.json({ error: 'Property not found' }, { status: 404 })
    }

    // Only property owner can approve
    if (Number(property.ownerId) !== userId) {
      return NextResponse.json({ error: 'Permission denied' }, { status: 403 })
    }

    const updated = await prisma.appointment.update({
      where: { id: appointmentId },
      data: {
        status: 'approved'
      }
    })

    // Send approval notice email to tenant
    sendEmail({
      to: appointment.email || '',
      subject: '✓ [BDS Rental] Lịch hẹn xem nhà của bạn đã được chấp thuận',
      html: getTenantApprovalHtml(updated, property, property.owner)
    }).catch(err => console.error('Error sending tenant approval email:', err))

    return NextResponse.json({
      success: true,
      message: 'Appointment approved successfully',
      appointment: updated
    })
  } catch (error: any) {
    console.error('Approve appointment error:', error)
    return NextResponse.json({ error: error.message }, { status: 500 })
  }
}
