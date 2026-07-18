const dotenv = require('dotenv')
const path = require('path')

dotenv.config({ path: path.join(__dirname, '../.env') })

async function testChatbotLeadSync() {
  const token = process.env.SCRM_API_TOKEN || '01KWKATNQGB5TWXYDPJ671X3X1'
  const apiUrl = process.env.SCRM_API_URL || 'https://sdata.io.vn/wp-json/scrmai/v1'

  const message = 'Chào bạn, tôi là Nguyễn Văn A, SĐT của tôi là 0912345678. Tôi muốn thuê căn hộ ở Quận 10.'
  const replyText = 'Dạ cảm ơn anh A, tôi đã ghi nhận SĐT 0912345678.'
  const history = [
    { role: 'user', content: 'Tôi muốn tìm thuê nhà' },
    { role: 'assistant', content: 'Chào bạn, bạn muốn thuê ở khu vực nào?' }
  ]

  console.log('Testing syncChatbotLeadToCrm logic...')

  try {
    const phoneRegex = /(0[3|5|7|8|9][0-9]{8})\b/
    const emailRegex = /[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/

    const phoneMatch = message.match(phoneRegex) || replyText.match(phoneRegex)
    if (!phoneMatch) {
      console.log('Error: Phone number not found in message or reply!')
      return
    }

    const phone = phoneMatch[1]
    const emailMatch = message.match(emailRegex) || replyText.match(emailRegex)
    const email = emailMatch ? emailMatch[0] : ''

    let name = 'Khách hàng Chatbot'
    const nameMatch = message.match(/(?:tên là|tôi tên|là|tên tôi là)\s*([A-ZÀ-Ỹa-zà-ỹ\s]{2,30})/i)
    if (nameMatch) {
      name = nameMatch[1].trim()
    }

    let demand = message
    if (history && Array.isArray(history) && history.length > 0) {
      demand = history.map((h) => h.role === 'user' ? h.content : '').filter(Boolean).join(' | ').slice(-200)
    }

    console.log('Extracted details:')
    console.log('- Name:', name)
    console.log('- Phone:', phone)
    console.log('- Email:', email)
    console.log('- Demand:', demand)

    // Bypass SSL rejection
    process.env.NODE_TLS_REJECT_UNAUTHORIZED = '0'

    console.log('Sending request to /lead/create...')
    const response = await fetch(`${apiUrl}/lead/create`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        title: `${name} - ${phone}`,
        acf: {
          name: name,
          phone: phone,
          email: email,
          zalo: phone,
          demand: demand,
          source: {
            slug: 'chatbot',
            name: 'AI Chatbot'
          },
          note: 'Khách hàng tiềm năng từ cuộc gọi/chat với AI Assistant'
        }
      })
    })

    console.log('Response Status:', response.status)
    const text = await response.text()
    console.log('Response Body:', text)
  } catch (error) {
    console.error('Fetch error:', error)
  }
}

testChatbotLeadSync()
