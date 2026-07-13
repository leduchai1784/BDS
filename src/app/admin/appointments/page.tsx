import { prisma } from '@/lib/prisma'
import AppointmentsTable from '@/components/admin/AppointmentsTable'

export const dynamic = 'force-dynamic'

interface AdminAppointmentsPageProps {
  searchParams: Promise<{
    search?: string
    status?: string
  }>
}

export default async function AdminAppointmentsPage({ searchParams }: AdminAppointmentsPageProps) {
  const resolvedSearchParams = await searchParams
  const search = resolvedSearchParams.search || ''
  const status = resolvedSearchParams.status || ''

  const where: any = {}

  if (status) {
    where.status = status
  }

  let appWhereClause = { ...where }
  
  if (search) {
    const props = await prisma.property.findMany({
      where: {
        title: { contains: search, mode: 'insensitive' }
      },
      select: { id: true }
    })
    const propIds = props.map(p => p.id)

    appWhereClause.OR = [
      { name: { contains: search, mode: 'insensitive' } },
      { phone: { contains: search, mode: 'insensitive' } },
      { propertyId: { in: propIds } }
    ]
  }

  const dbAppointments = await prisma.appointment.findMany({
    where: appWhereClause,
    orderBy: { createdAt: 'desc' }
  })

  // Fetch unique properties to join in JS memory
  const matchedPropIds = Array.from(new Set(dbAppointments.map(a => a.propertyId)))
  const propertiesList = await prisma.property.findMany({
    where: { id: { in: matchedPropIds } }
  })

  const appointmentsList = dbAppointments.map(app => {
    const p = propertiesList.find(x => x.id === app.propertyId)
    return {
      id: app.id.toString(),
      name: app.name,
      phone: app.phone,
      email: app.email || '',
      date: app.date ? new Date(app.date).toLocaleDateString('vi-VN') : '',
      time: app.time instanceof Date 
        ? app.time.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit', hour12: false }) 
        : String(app.time),
      message: app.message,
      status: app.status,
      rejectReason: app.rejectReason,
      property: p ? {
        title: p.title,
        address: p.address
      } : {
        title: 'Bất động sản',
        address: 'Liên hệ'
      }
    }
  })

  return (
    <div className="space-y-6">
      
      {/* Title Header */}
      <div className="text-left">
        <h1 className="text-xl font-bold text-slate-800">Quản lý lịch hẹn</h1>
        <p className="text-xs text-slate-400 mt-1 font-semibold">Theo dõi tất cả lịch hẹn xem nhà phố, căn hộ của khách hàng và điều phối khi cần thiết.</p>
      </div>

      <AppointmentsTable 
        initialAppointments={appointmentsList} 
        searchParams={{ search, status }} 
      />

    </div>
  )
}
