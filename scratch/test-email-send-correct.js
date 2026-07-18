const dotenv = require('dotenv')
dotenv.config({ path: '.env.local' })
dotenv.config({ path: '.env' })

// Now import after loading env
const { sendEmail } = require('../src/lib/mail')

async function testMail() {
  console.log('Using SMTP Configuration:')
  console.log('SMTP_HOST:', process.env.SMTP_HOST)
  console.log('SMTP_PORT:', process.env.SMTP_PORT)
  console.log('SMTP_USER:', process.env.SMTP_USER)
  console.log('SMTP_PASSWORD:', process.env.SMTP_PASSWORD ? '******' : 'MISSING')
  console.log('MAIL_FROM:', process.env.MAIL_FROM)
  
  const testRecipient = process.env.SMTP_USER || 'lehai17082004@gmail.com'
  console.log(`Sending test email to ${testRecipient}...`)

  const res = await sendEmail({
    to: testRecipient,
    subject: '🧪 Test Email BDS Rental',
    html: `
      <h1>BDS Rental Test Email</h1>
      <p>This is a test email to verify SMTP connection and authentication.</p>
      <p>Sent at: ${new Date().toISOString()}</p>
    `
  })

  if (res.success) {
    console.log('✅ Test email sent successfully!')
  } else {
    console.error('❌ Test email sending failed! Error:', res.error)
  }
}

testMail()
