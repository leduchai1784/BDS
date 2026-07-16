import { NextResponse } from 'next/server'
import { auth } from '@/lib/auth'
import { prisma } from '@/lib/prisma'
import { 
  sendEmail, 
  getTenantConfirmationHtml, 
  getOwnerNotificationHtml, 
  getAdminNotificationHtml 
} from '@/lib/mail'

export async function POST(req: Request) {
  try {
    const session = await auth()
    if (!session?.user?.id) {
      return NextResponse.json({ error: 'Unauthorized' }, { status: 401 })
    }

    const userId = Number(session.user.id)
    const { property_id, name, phone, email, date, time, message } = await req.json()

    if (!property_id || !name || !phone || !email || !date || !time) {
      return NextResponse.json({ error: 'Missing required parameters' }, { status: 400 })
    }

    const isUuid = /^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i.test(property_id)
    if (!isUuid) {
      return NextResponse.json({ error: 'Property not found' }, { status: 404 })
    }

    // Find property and owner details
    const property = await prisma.property.findUnique({
      where: { id: property_id },
      include: {
        owner: true
      }
    })

    if (!property) {
      return NextResponse.json({ error: 'Property not found' }, { status: 404 })
    }

    // Owner cannot book their own property
    if (Number(property.ownerId) === userId) {
      return NextResponse.json({ error: 'Bạn không thể tự đặt lịch xem tin đăng của chính mình.' }, { status: 400 })
    }

    // Create the appointment in database
    const appointment = await prisma.appointment.create({
      data: {
        propertyId: property_id,
        userId,
        name,
        phone,
        email,
        date: new Date(date),
        time: new Date(`1970-01-01T${time}:00Z`),
        message,
        status: 'approved'
      }
    })

    // Send all emails in parallel and await their completion (required for serverless envs to prevent freezing)
    const owner = property.owner
    const adminEmail = process.env.SMTP_USER || 'admin@bdsrental.vn'

    await Promise.allSettled([
      sendEmail({
        to: email,
        subject: '✅ [BDS Rental] Đặt lịch hẹn xem nhà thành công',
        html: getTenantConfirmationHtml(appointment, property, owner)
      }),
      owner?.email ? sendEmail({
        to: owner.email,
        subject: '🔔 [BDS Rental] Có khách hàng đặt lịch xem nhà của bạn',
        html: getOwnerNotificationHtml(appointment, property)
      }) : Promise.resolve(),
      sendEmail({
        to: adminEmail,
        subject: '🔔 [BDS Rental] Có lịch hẹn xem nhà mới trên hệ thống',
        html: getAdminNotificationHtml(appointment, property, owner)
      })
    ])

    // Push this guest to the external SCRM CRM API as a new lead
    try {
      const token = process.env.SCRM_API_TOKEN
      const apiUrl = process.env.SCRM_API_URL || 'https://sdata.io.vn/wp-json/scrmai/v1'
      if (token) {
        await fetch(`${apiUrl}/lead/create`, {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            title: `${name} - ${phone}`,
            acf: {
              name: name,
              phone: phone,
              email: email,
              zalo: phone,
              demand: `Đặt lịch hẹn xem nhà: ${property?.title || 'BĐS'}. Lời nhắn: ${message || 'Không có'}`,
              source: {
                slug: 'website',
                name: 'Website'
              },
              note: `Lịch hẹn xem nhà ngày ${date} lúc ${time}`
            }
          })
        })
      }
    } catch (e) {
      console.error('Failed to sync appointment lead to SCRM CRM API:', e)
    }

    return NextResponse.json({
      success: true,
      message: 'Booking created successfully',
      appointment
    })
  } catch (error: any) {
    console.error('Booking API error:', error)
    return NextResponse.json({ error: error.message }, { status: 500 })
  }
}
