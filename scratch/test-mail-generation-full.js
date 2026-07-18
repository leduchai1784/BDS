const { 
  getTenantConfirmationHtml, 
  getTenantCancellationHtml,
  getTenantApprovalHtml,
  getTenantRejectionHtml,
  getOwnerNotificationHtml, 
  getOwnerCancellationHtml,
  getAdminNotificationHtml 
} = require('../src/lib/mail')

const appointment = {
  id: 123n,
  name: 'Lê Đức Hải',
  phone: '0912345678',
  email: 'test@example.com',
  date: new Date('2026-07-20'),
  time: new Date('1970-01-01T09:30:00Z'),
  message: 'Hello note',
  status: 'approved'
}

const property = {
  title: 'Căn hộ Vinhomes Central Park - View trực diện Sông Sài Gòn',
  address: '208 Nguyễn Hữu Cảnh, Bình Thạnh'
}

const owner = {
  name: 'Nguyễn Văn Chủ Nhà',
  phone: '0987654321',
  email: 'owner@example.com'
}

try {
  console.log('Generating Tenant Confirmation HTML...')
  getTenantConfirmationHtml(appointment, property, owner)
  
  console.log('Generating Tenant Cancellation HTML...')
  getTenantCancellationHtml(appointment, property, owner)

  console.log('Generating Tenant Approval HTML...')
  getTenantApprovalHtml(appointment, property, owner)

  console.log('Generating Tenant Rejection HTML...')
  getTenantRejectionHtml(appointment, property, owner, 'Chủ nhà bận')

  console.log('Generating Owner Notification HTML...')
  getOwnerNotificationHtml(appointment, property)

  console.log('Generating Owner Cancellation HTML...')
  getOwnerCancellationHtml(appointment, property)

  console.log('Generating Admin HTML...')
  getAdminNotificationHtml(appointment, property, owner)

  console.log('✅ All email templates generated successfully!')
} catch (err) {
  console.error('❌ Generation Crash:', err)
}
