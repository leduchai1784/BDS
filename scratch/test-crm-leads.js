const dotenv = require('dotenv')
const path = require('path')

// Load .env
dotenv.config({ path: path.join(__dirname, '../.env') })

async function testFetchLeads() {
  const token = process.env.SCRM_API_TOKEN
  const apiUrl = process.env.SCRM_API_URL || 'https://sdata.io.vn/wp-json/scrmai/v1'

  console.log('API URL:', apiUrl)
  console.log('Token:', token ? `${token.substring(0, 5)}...` : 'undefined')

  if (!token) {
    console.log('Error: SCRM_API_TOKEN is not defined in .env')
    return
  }

  try {
    const controller = new AbortController()
    const timeoutId = setTimeout(() => controller.abort(), 8000)

    const response = await fetch(`${apiUrl}/leads`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      },
      signal: controller.signal
    })

    clearTimeout(timeoutId)

    console.log('Response Status:', response.status)
    
    if (response.ok) {
      const data = await response.json()
      console.log('Response JSON success:', data?.success)
      console.log('Leads count returned:', Array.isArray(data?.data) ? data.data.length : 'Not an array')
      if (Array.isArray(data?.data)) {
        // Sort by ID descending to see newest first
        const sorted = [...data.data].sort((a, b) => b.id - a.id)
        sorted.slice(0, 5).forEach((lead, i) => {
          console.log(`Lead #${i + 1}: ID=${lead.id}, Title="${lead.title}", CreatedAt="${lead.created_at}"`)
          console.log('  ACF:', JSON.stringify(lead.acf, null, 2))
        })
      }
    } else {
      const text = await response.text()
      console.log('Response Body:', text)
    }
  } catch (error) {
    console.error('Fetch error:', error.message)
  }
}

testFetchLeads()
