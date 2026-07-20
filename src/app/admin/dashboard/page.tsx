import React from "react"
import { prisma } from "@/lib/prisma"
import { EcommerceMetrics } from "@/components/admin/dashboard/ecommerce/EcommerceMetrics"
import MonthlyTarget from "@/components/admin/dashboard/ecommerce/MonthlyTarget"
import MonthlySalesChart from "@/components/admin/dashboard/ecommerce/MonthlySalesChart"
import StatisticsChart from "@/components/admin/dashboard/ecommerce/StatisticsChart"
import RecentOrders from "@/components/admin/dashboard/ecommerce/RecentOrders"
import DemographicCard from "@/components/admin/dashboard/ecommerce/DemographicCard"

export const dynamic = 'force-dynamic'

async function fetchExternalLeadsCount(): Promise<number> {
  try {
    process.env.NODE_TLS_REJECT_UNAUTHORIZED = '0'
    const token = process.env.SCRM_API_TOKEN || '01KWKATNQGB5TWXYDPJ671X3X1'
    const apiUrl = process.env.SCRM_API_URL || 'https://sdata.io.vn/wp-json/scrmai/v1'
    const response = await fetch(`${apiUrl}/leads`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      },
      next: { revalidate: 60 }
    })
    if (response.ok) {
      const data = await response.json()
      if (data?.success && Array.isArray(data.data)) {
        return data.data.length
      }
    }
    return 0
  } catch (e) {
    return 0
  }
}

export default async function AdminDashboardPage() {
  // Query actual data from database
  const userCount = await prisma.user.count()
  const propertyCount = await prisma.property.count()
  const appointmentCount = await prisma.appointment.count()
  const leadCount = await fetchExternalLeadsCount()

  // Thống kê tin đăng theo loại giao dịch (bán / cho thuê)
  const saleProperties = await prisma.property.count({ where: { transactionType: 'sale' } })
  const rentProperties = await prisma.property.count({ where: { transactionType: 'rent' } })

  // Lấy danh sách lịch hẹn gần nhất
  const rawAppointments = await prisma.appointment.findMany({
    orderBy: { createdAt: 'desc' },
    take: 5,
    include: { user: true }
  })

  const recentAppointments = rawAppointments.map((ap) => ({
    id: ap.id.toString(),
    user: {
      name: ap.name || ap.user?.name || "Khách hàng ẩn danh",
      email: ap.email || ap.user?.email || "Chưa cung cấp email",
      avatar: ap.user?.avatar || ""
    },
    propertyTitle: "Bất động sản #" + ap.propertyId.substring(0, 8),
    date: ap.date.toLocaleDateString('vi-VN'),
    status: ap.status === 'approved' ? 'Delivered' : ap.status === 'pending' ? 'Pending' : 'Cancelled'
  }))

  return (
    <div className="w-full text-slate-800 dark:text-slate-100 font-sans text-left">
      <div className="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h1 className="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Bảng điều khiển thống kê</h1>
          <p className="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">Tổng quan hoạt động và dữ liệu kinh doanh dự án từ cơ sở dữ liệu thật</p>
        </div>
      </div>

      <div className="grid grid-cols-12 gap-4 md:gap-6">
        <div className="col-span-12 space-y-6 xl:col-span-7">
          <EcommerceMetrics 
            users={userCount}
            properties={propertyCount}
            appointments={appointmentCount}
            leads={leadCount}
          />
          <MonthlySalesChart />
        </div>

        <div className="col-span-12 xl:col-span-5">
          <MonthlyTarget />
        </div>

        <div className="col-span-12">
          <StatisticsChart />
        </div>

        <div className="col-span-12 xl:col-span-5">
          <DemographicCard 
            sale={saleProperties}
            rent={rentProperties}
          />
        </div>

        <div className="col-span-12 xl:col-span-7">
          <RecentOrders appointments={recentAppointments} />
        </div>
      </div>
    </div>
  )
}
