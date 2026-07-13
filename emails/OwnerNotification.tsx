import * as React from 'react'

export interface OwnerNotificationEmailProps {
  name: string
  propertyTitle: string
  date: string
  time: string
}

export default function OwnerNotificationEmail({
  name = 'Chủ nhà',
  propertyTitle = 'Căn hộ chung cư',
  date = '12/07/2026',
  time = '09:00'
}: OwnerNotificationEmailProps) {
  return (
    <div style={{ fontFamily: 'sans-serif', padding: '20px', backgroundColor: '#f8fafc' }}>
      <div style={{ maxWidth: '600px', margin: '0 auto', backgroundColor: '#ffffff', borderRadius: '8px', border: '1px solid #e2e8f0', overflow: 'hidden' }}>
        <div style={{ backgroundColor: '#0077bb', padding: '20px', color: '#ffffff', textAlign: 'center' }}>
          <h2 style={{ margin: 0 }}>Có yêu cầu hẹn xem nhà mới</h2>
        </div>
        <div style={{ padding: '24px' }}>
          <p>Xin chào <strong>{name}</strong>,</p>
          <p>Khách hàng đã gửi yêu cầu xem bất động sản <strong>{propertyTitle}</strong> của bạn:</p>
          <ul>
            <li><strong>Thời gian mong muốn:</strong> {date} lúc {time}</li>
          </ul>
          <p>Vui lòng đăng nhập hệ thống để xác nhận cuộc hẹn.</p>
        </div>
      </div>
    </div>
  )
}
