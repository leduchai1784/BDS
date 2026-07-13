import * as React from 'react'

export interface TenantRejectionEmailProps {
  name: string
  propertyTitle: string
  reason: string
}

export default function TenantRejectionEmail({
  name = 'Khách hàng',
  propertyTitle = 'Căn hộ chung cư',
  reason = 'Chủ nhà bận đột xuất'
}: TenantRejectionEmailProps) {
  return (
    <div style={{ fontFamily: 'sans-serif', padding: '20px', backgroundColor: '#f8fafc' }}>
      <div style={{ maxWidth: '600px', margin: '0 auto', backgroundColor: '#ffffff', borderRadius: '8px', border: '1px solid #e2e8f0', overflow: 'hidden' }}>
        <div style={{ backgroundColor: '#f59e0b', padding: '20px', color: '#ffffff', textAlign: 'center' }}>
          <h2 style={{ margin: 0 }}>Lịch hẹn đã bị từ chối</h2>
        </div>
        <div style={{ padding: '24px' }}>
          <p>Xin chào <strong>{name}</strong>,</p>
          <p>Lịch hẹn xem bất động sản <strong>{propertyTitle}</strong> của bạn đã bị từ chối với lý do:</p>
          <blockquote style={{ padding: '10px 20px', margin: '10px 0', backgroundColor: '#fffbeb', borderLeft: '4px solid #f59e0b' }}>
            {reason}
          </blockquote>
        </div>
      </div>
    </div>
  )
}
