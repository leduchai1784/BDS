import * as React from 'react'

export interface OwnerCancellationEmailProps {
  name: string
  propertyTitle: string
}

export default function OwnerCancellationEmail({
  name = 'Chủ nhà',
  propertyTitle = 'Căn hộ chung cư'
}: OwnerCancellationEmailProps) {
  return (
    <div style={{ fontFamily: 'sans-serif', padding: '20px', backgroundColor: '#f8fafc' }}>
      <div style={{ maxWidth: '600px', margin: '0 auto', backgroundColor: '#ffffff', borderRadius: '8px', border: '1px solid #e2e8f0', overflow: 'hidden' }}>
        <div style={{ backgroundColor: '#ef4444', padding: '20px', color: '#ffffff', textAlign: 'center' }}>
          <h2 style={{ margin: 0 }}>Lịch hẹn đã bị hủy bởi khách</h2>
        </div>
        <div style={{ padding: '24px' }}>
          <p>Xin chào <strong>{name}</strong>,</p>
          <p>Chúng tôi xin thông báo cuộc hẹn xem bất động sản <strong>{propertyTitle}</strong> của bạn đã bị hủy bỏ bởi khách hàng.</p>
        </div>
      </div>
    </div>
  )
}
