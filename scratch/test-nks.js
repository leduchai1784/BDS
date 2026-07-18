const dotenv = require('dotenv')
const path = require('path')
const axios = require('axios')

dotenv.config({ path: path.join(__dirname, '../.env') })

async function testNksLogin() {
  const email = 'lehai17082004@gmail.com'
  const password = '12345678'
  process.env.NODE_TLS_REJECT_UNAUTHORIZED = '0'

  const baseUrl = process.env.NKS_AUTH_BASE_URL || 'https://account.nks.vn/api/nks/user'
  const loginUrl = `${baseUrl}/login`

  console.log(`Connecting to: ${loginUrl}`)

  try {
    const response = await axios.post(loginUrl, {
      username: email,
      password: password
    }, { timeout: 8000 })
    console.log('Response status:', response.status)
    console.log('Response body:', JSON.stringify(response.data, null, 2))
  } catch (err) {
    console.log('Request failed!')
    if (err.response) {
      console.log('Status:', err.response.status)
      console.log('Response:', JSON.stringify(err.response.data))
    } else {
      console.log('Error Message:', err.message)
    }
  }
}

testNksLogin()
