const dotenv = require('dotenv')
const path = require('path')

dotenv.config({ path: path.join(__dirname, '../.env') })

async function testSourceFormats() {
  const token = process.env.SCRM_API_TOKEN || '01KWKATNQGB5TWXYDPJ671X3X1'
  const apiUrl = process.env.SCRM_API_URL || 'https://sdata.io.vn/wp-json/scrmai/v1'

  // We will test 4 formats:
  // Format A: 'website' (string slug)
  // Format B: 'chatbot' (string slug)
  // Format C: 31 (number ID for Website)
  // Format D: [31] (array of IDs)
  // Format E: ['chatbot'] (array of slugs)

  const formats = [
    { label: 'String chatbot', value: 'chatbot' },
    { label: 'String website', value: 'website' },
    { label: 'Number 31', value: 31 },
    { label: 'Array [31]', value: [31] },
    { label: 'Array [website]', value: ['website'] }
  ]

  process.env.NODE_TLS_REJECT_UNAUTHORIZED = '0'

  for (const fmt of formats) {
    console.log(`\nTesting format: ${fmt.label}`)
    try {
      const response = await fetch(`${apiUrl}/lead/create`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          title: `Test source format: ${fmt.label}`,
          acf: {
            name: `Test ${fmt.label}`,
            phone: '0900000000',
            source: fmt.value
          }
        })
      })

      const resJson = await response.json()
      console.log('Result:', resJson)

      if (resJson.success && resJson.id) {
        // Fetch it back to see what got saved
        const fetchRes = await fetch(`${apiUrl}/leads`, {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
          }
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

testSourceFormats()
