const { getTenantConfirmationHtml, getOwnerNotificationHtml, getAdminNotificationHtml } = require('../src/lib/mail')

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
  console.log('Generating Tenant HTML...')
  const html1 = getTenantConfirmationHtml(appointment, property, owner)
  console.log('Tenant HTML Generated successfully! Length:', html1.length)

  console.log('Generating Owner HTML...')
  const html2 = getOwnerNotificationHtml(appointment, property)
  console.log('Owner HTML Generated successfully! Length:', html2.length)

  console.log('Generating Admin HTML...')
  const html3 = getAdminNotificationHtml(appointment, property, owner)
  console.log('Admin HTML Generated successfully! Length:', html3.length)

} catch (err) {
  console.error('Generation Crash:', err)
}
