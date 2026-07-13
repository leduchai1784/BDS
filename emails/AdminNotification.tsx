import * as React from 'react'

export interface AdminNotificationEmailProps {
  propertyTitle: string
  tenantName: string
  ownerName: string
  date: string
  time: string
}

export default function AdminNotificationEmail({
  propertyTitle = 'Căn hộ chung cư',
  tenantName = 'Khách hàng',
  ownerName = 'Chủ nhà',
  date = '12/07/2026',
  time = '09:00'
}: AdminNotificationEmailProps) {
  return (
    <div style={{ fontFamily: 'sans-serif', padding: '20px', backgroundColor: '#f8fafc' }}>
      <div style={{ maxWidth: '600px', margin: '0 auto', backgroundColor: '#ffffff', borderRadius: '8px', border: '1px solid #e2e8f0', overflow: 'hidden' }}>
        <div style={{ backgroundColor: '#0f172a', padding: '20px', color: '#ffffff', textAlign: 'center' }}>
          <h2 style={{ margin: 0 }}>[ADMIN] Có lịch hẹn mới đăng ký</h2>
        </div>
        <div style={{ padding: '24px' }}>
          <p>Xin chào Admin,</p>
          <p>Lịch hẹn xem nhà mới vừa được đăng ký thành công trên hệ thống:</p>
          <ul>
            <li><strong>Bất động sản:</strong> {propertyTitle}</li>
            <li><strong>Khách hàng:</strong> {tenantName}</li>
            <li><strong>Chủ nhà:</strong> {ownerName}</li>
            <li><strong>Thời gian:</strong> {date} lúc {time}</li>
          </ul>
        </div>
      </div>
    </div>
  )
}
