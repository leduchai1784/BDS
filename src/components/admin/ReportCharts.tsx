'use client'

import {
  BarChart,
  Bar,
  XAxis,
  YAxis,
  CartesianGrid,
  Tooltip,
  ResponsiveContainer,
  PieChart,
  Pie,
  Cell,
  Legend
} from 'recharts'

interface CategoryShare {
  name: string
  value: number
}

interface MonthlyTrend {
  month: string
  properties: number
  appointments: number
}

interface ReportChartsProps {
  categoryShare: CategoryShare[]
  monthlyTrends: MonthlyTrend[]
}

const COLORS = ['#0077bb', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#64748b']

export default function ReportCharts({ categoryShare, monthlyTrends }: ReportChartsProps) {
  return (
    <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 text-left">
      
      {/* 1. Bar Chart: Monthly Trends */}
      <div className="bg-white border border-slate-100 rounded-3xl p-5 sm:p-6 shadow-sm">
        <h3 className="text-xs font-black text-slate-800 uppercase tracking-wider mb-4">Biến động tin đăng & Lịch hẹn</h3>
        <div className="w-full h-72">
          <ResponsiveContainer width="100%" height="100%">
            <BarChart data={monthlyTrends} margin={{ top: 10, right: 10, left: -20, bottom: 0 }}>
              <CartesianGrid strokeDasharray="3 3" stroke="#f1f5f9" />
              <XAxis dataKey="month" stroke="#94a3b8" fontSize={11} fontWeight={600} />
              <YAxis stroke="#94a3b8" fontSize={11} fontWeight={600} />
              <Tooltip 
                contentStyle={{ backgroundColor: '#1e293b', border: 'none', borderRadius: '12px', color: '#fff' }}
                labelStyle={{ fontWeight: 'black', fontSize: '12px' }}
              />
              <Legend verticalAlign="top" height={36} iconType="circle" wrapperStyle={{ fontSize: '11px', fontWeight: 'bold' }} />
              <Bar dataKey="properties" name="Tin đăng" fill="#0077bb" radius={[4, 4, 0, 0]} />
              <Bar dataKey="appointments" name="Lịch hẹn" fill="#10b981" radius={[4, 4, 0, 0]} />
            </BarChart>
          </ResponsiveContainer>
        </div>
      </div>

      {/* 2. Pie Chart: Property types / Category distributions */}
      <div className="bg-white border border-slate-100 rounded-3xl p-5 sm:p-6 shadow-sm">
        <h3 className="text-xs font-black text-slate-800 uppercase tracking-wider mb-4">Cơ cấu tin đăng theo Danh mục</h3>
        <div className="w-full h-72">
          <ResponsiveContainer width="100%" height="100%">
            <PieChart>
              <Pie
                data={categoryShare}
                cx="50%"
                cy="45%"
                innerRadius={60}
                outerRadius={85}
                paddingAngle={4}
                dataKey="value"
              >
                {categoryShare.map((entry, index) => (
                  <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]} />
                ))}
              </Pie>
              <Tooltip
                contentStyle={{ backgroundColor: '#1e293b', border: 'none', borderRadius: '12px', color: '#fff' }}
                itemStyle={{ fontSize: '11px', fontWeight: 'bold' }}
              />
              <Legend 
                verticalAlign="bottom" 
                height={36} 
                iconType="circle" 
                wrapperStyle={{ fontSize: '10px', fontWeight: 'bold' }} 
              />
            </PieChart>
          </ResponsiveContainer>
        </div>
      </div>

    </div>
  )
}
