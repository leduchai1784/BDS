'use client'

import {
  LineChart,
  Line,
  XAxis,
  YAxis,
  CartesianGrid,
  Tooltip,
  ResponsiveContainer,
  Legend,
  BarChart,
  Bar
} from 'recharts'

interface ChartItem {
  month: string
  properties: number
  users: number
  appointments: number
}

interface DashboardChartsProps {
  data: ChartItem[]
}

export default function DashboardCharts({ data }: DashboardChartsProps) {
  return (
    <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
      
      {/* 1. Main Line Chart: Monthly Growths */}
      <div className="bg-white border border-slate-100 rounded-3xl p-5 sm:p-6 shadow-sm lg:col-span-2 text-left">
        <h4 className="text-xs font-black text-slate-800 uppercase tracking-wider mb-4">Biểu đồ tăng trưởng 6 tháng</h4>
        <div className="w-full h-80">
          <ResponsiveContainer width="100%" height="100%">
            <LineChart data={data} margin={{ top: 10, right: 10, left: -20, bottom: 0 }}>
              <CartesianGrid strokeDasharray="3 3" stroke="#f1f5f9" />
              <XAxis dataKey="month" stroke="#94a3b8" fontSize={11} fontWeight={600} />
              <YAxis stroke="#94a3b8" fontSize={11} fontWeight={600} />
              <Tooltip 
                contentStyle={{ backgroundColor: '#1e293b', border: 'none', borderRadius: '12px', color: '#fff' }}
                labelStyle={{ fontWeight: 'black', fontSize: '12px' }}
              />
              <Legend verticalAlign="top" height={36} iconType="circle" wrapperStyle={{ fontSize: '11px', fontWeight: 'bold' }} />
              <Line type="monotone" dataKey="properties" name="Tin đăng mới" stroke="#0077bb" strokeWidth={3} dot={{ r: 4 }} activeDot={{ r: 6 }} />
              <Line type="monotone" dataKey="users" name="Thành viên mới" stroke="#ef4444" strokeWidth={3} dot={{ r: 4 }} activeDot={{ r: 6 }} />
              <Line type="monotone" dataKey="appointments" name="Lịch hẹn mới" stroke="#10b981" strokeWidth={3} dot={{ r: 4 }} activeDot={{ r: 6 }} />
            </LineChart>
          </ResponsiveContainer>
        </div>
      </div>

      {/* 2. Bar Chart: Comparative Overview */}
      <div className="bg-white border border-slate-100 rounded-3xl p-5 sm:p-6 shadow-sm text-left">
        <h4 className="text-xs font-black text-slate-800 uppercase tracking-wider mb-4">Thống kê tương quan</h4>
        <div className="w-full h-80">
          <ResponsiveContainer width="100%" height="100%">
            <BarChart data={data} margin={{ top: 10, right: 5, left: -20, bottom: 0 }}>
              <CartesianGrid strokeDasharray="3 3" stroke="#f1f5f9" />
              <XAxis dataKey="month" stroke="#94a3b8" fontSize={11} fontWeight={600} />
              <YAxis stroke="#94a3b8" fontSize={11} fontWeight={600} />
              <Tooltip
                contentStyle={{ backgroundColor: '#1e293b', border: 'none', borderRadius: '12px', color: '#fff' }}
                labelStyle={{ fontWeight: 'black', fontSize: '12px' }}
              />
              <Legend verticalAlign="top" height={36} iconType="rect" wrapperStyle={{ fontSize: '11px', fontWeight: 'bold' }} />
              <Bar dataKey="properties" name="Tin đăng" fill="#0077bb" radius={[4, 4, 0, 0]} />
              <Bar dataKey="appointments" name="Lịch đặt" fill="#10b981" radius={[4, 4, 0, 0]} />
            </BarChart>
          </ResponsiveContainer>
        </div>
      </div>

    </div>
  )
}
