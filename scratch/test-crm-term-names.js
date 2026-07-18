const dotenv = require('dotenv')
const path = require('path')

dotenv.config({ path: path.join(__dirname, '../.env') })

async function testTermNames() {
  const token = process.env.SCRM_API_TOKEN || '01KWKATNQGB5TWXYDPJ671X3X1'
  const apiUrl = process.env.SCRM_API_URL || 'https://sdata.io.vn/wp-json/scrmai/v1'

  const testPayloads = [
    { label: 'String "Website"', val: 'Website' },
    { label: 'String "AI Chatbot"', val: 'AI Chatbot' },
    { label: 'String "Chatbot"', val: 'Chatbot' }
  ]

  process.env.NODE_TLS_REJECT_UNAUTHORIZED = '0'

  for (const item of testPayloads) {
    console.log(`\nTesting: ${item.label}`)
    try {
      const response = await fetch(`${apiUrl}/lead/create`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          title: `Test Name: ${item.label}`,
          acf: {
            name: `Test ${item.label}`,
            phone: '0912345678',
            source: item.val
          }
        })
      })

      const resJson = await response.json()
      console.log('Create result:', resJson)

      if (resJson.success && resJson.id) {
        await new Promise(r => setTimeout(r, 4000))
        const fetchRes = await fetch(`${apiUrl}/leads`, {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
          },
          cache: 'no-store'
        })
        const leadsData = await fetchRes.json()
        const savedLead = leadsData.data.find(l => l.id === resJson.id)
        console.log('Saved source value in ACF:', savedLead?.acf?.source)

        // Delete the temp lead
        await fetch(`${apiUrl}/lead/delete`, {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ id: resJson.id })
        })
        console.log('Deleted temp lead.')
      }
    } catch (e) {
      console.error('Error:', e.message)
    }
  }
}

testTermNames()
