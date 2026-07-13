import * as React from 'react'

export interface TenantConfirmationEmailProps {
  name: string
  propertyTitle: string
  date: string
  time: string
}

export default function TenantConfirmationEmail({
  name = 'Khách hàng',
  propertyTitle = 'Căn hộ chung cư',
  date = '12/07/2026',
  time = '09:00'
}: TenantConfirmationEmailProps) {
  return (
    <div style={{ fontFamily: 'sans-serif', padding: '20px', backgroundColor: '#f8fafc' }}>
      <div style={{ maxWidth: '600px', margin: '0 auto', backgroundColor: '#ffffff', borderRadius: '8px', border: '1px solid #e2e8f0', overflow: 'hidden' }}>
        <div style={{ backgroundColor: '#0077bb', padding: '20px', color: '#ffffff', textAlign: 'center' }}>
          <h2 style={{ margin: 0 }}>Xác nhận lịch hẹn xem nhà</h2>
        </div>
        <div style={{ padding: '24px' }}>
          <p>Xin chào <strong>{name}</strong>,</p>
          <p>Lịch hẹn xem bất động sản của bạn đã được ghi nhận trên hệ thống:</p>
          <ul>
            <li><strong>Bất động sản:</strong> {propertyTitle}</li>
            <li><strong>Thời gian:</strong> {date} lúc {time}</li>
          </ul>
          <p>Chủ nhà sẽ liên hệ trực tiếp với bạn sớm để xác nhận lịch gặp.</p>
        </div>
      </div>
    </div>
  )
}
