const dotenv = require('dotenv')
const path = require('path')

dotenv.config({ path: path.join(__dirname, '../.env') })

async function testCreateLead() {
  const token = process.env.SCRM_API_TOKEN
  const apiUrl = process.env.SCRM_API_URL || 'https://sdata.io.vn/wp-json/scrmai/v1'

  const leadData = {
    title: 'Test Lead from Script',
    acf: {
      name: 'Test Khách Hàng',
      phone: '0999999999',
      email: 'testlead@gmail.com',
      zalo: '0999999999',
      demand: 'Thuê căn hộ Quận 10 dưới 15tr',
      source: {
        slug: 'website',
        name: 'Website'
      },
      note: 'Ghi chú thử nghiệm từ script'
    }
  }

  console.log('Sending payload to /lead/create:', JSON.stringify(leadData, null, 2))

  try {
    const response = await fetch(`${apiUrl}/lead/create`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(leadData)
    })

    console.log('Status:', response.status)
    const text = await response.text()
    console.log('Response:', text)
  } catch (error) {
    console.error('Error creating lead:', error.message)
  }
}

testCreateLead()
