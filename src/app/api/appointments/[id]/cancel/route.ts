import { NextResponse } from 'next/server'
import { auth } from '@/lib/auth'
import { prisma } from '@/lib/prisma'
import { sendEmail, getTenantCancellationHtml, getOwnerCancellationHtml } from '@/lib/mail'

export async function POST(
  req: Request,
  { params }: { params: Promise<{ id: string }> }
) {
  try {
    const session = await auth()
    if (!session?.user?.id) {
      return NextResponse.json({ error: 'Unauthorized' }, { status: 401 })
    }

    const resolvedParams = await params
    const appointmentId = Number(resolvedParams.id)
    const userId = Number(session.user.id)

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

    // Tenant or Owner can cancel
    const isTenant = appointment.userId ? Number(appointment.userId) === userId : false
    const isOwner = Number(property.ownerId) === userId

    if (!isTenant && !isOwner) {
      return NextResponse.json({ error: 'Permission denied' }, { status: 403 })
    }

    // Update appointment status
    const updated = await prisma.appointment.update({
      where: { id: appointmentId },
      data: {
        status: 'cancelled'
      }
    })

    // Send emails and await completion (required for serverless envs)
    const owner = property.owner

    await Promise.allSettled([
      sendEmail({
        to: appointment.email || '',
        subject: '❌ [BDS Rental] Lịch hẹn xem nhà đã bị hủy thành công',
        html: getTenantCancellationHtml(updated, property, owner)
      }),
      owner?.email ? sendEmail({
        to: owner.email,
        subject: '❌ [BDS Rental] Lịch hẹn xem nhà đã bị hủy',
        html: getOwnerCancellationHtml(updated, property)
      }) : Promise.resolve()
    ])

    return NextResponse.json({
      success: true,
      message: 'Appointment cancelled successfully',
      appointment: updated
    })
  } catch (error: any) {
    console.error('Cancel appointment error:', error)
    return NextResponse.json({ error: error.message }, { status: 500 })
  }
}
