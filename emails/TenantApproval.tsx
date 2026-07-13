import * as React from 'react'

export interface TenantApprovalEmailProps {
  name: string
  propertyTitle: string
  date: string
  time: string
}

export default function TenantApprovalEmail({
  name = 'Khách hàng',
  propertyTitle = 'Căn hộ chung cư',
  date = '12/07/2026',
  time = '09:00'
}: TenantApprovalEmailProps) {
  return (
    <div style={{ fontFamily: 'sans-serif', padding: '20px', backgroundColor: '#f8fafc' }}>
      <div style={{ maxWidth: '600px', margin: '0 auto', backgroundColor: '#ffffff', borderRadius: '8px', border: '1px solid #e2e8f0', overflow: 'hidden' }}>
        <div style={{ backgroundColor: '#10b981', padding: '20px', color: '#ffffff', textAlign: 'center' }}>
          <h2 style={{ margin: 0 }}>Lịch hẹn đã được xác nhận</h2>
        </div>
        <div style={{ padding: '24px' }}>
          <p>Xin chào <strong>{name}</strong>,</p>
          <p>Lịch hẹn xem bất động sản <strong>{propertyTitle}</strong> của bạn đã được chủ nhà chấp nhận.</p>
          <p>Thời gian gặp: 📅 {date} lúc ⏰ {time}.</p>
        </div>
      </div>
    </div>
  )
}
