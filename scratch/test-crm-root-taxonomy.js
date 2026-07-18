const dotenv = require('dotenv')
const path = require('path')

dotenv.config({ path: path.join(__dirname, '../.env') })

async function testRootTaxonomy() {
  const token = process.env.SCRM_API_TOKEN || '01KWKATNQGB5TWXYDPJ671X3X1'
  const apiUrl = process.env.SCRM_API_URL || 'https://sdata.io.vn/wp-json/scrmai/v1'

  const rootKeys = [
    { label: 'Root source: [31]', payload: { source: [31] } },
    { label: 'Root lead_source: [31]', payload: { lead_source: [31] } },
    { label: 'Root lead-source: [31]', payload: { 'lead-source': [31] } },
    { label: 'Root source: 31', payload: { source: 31 } },
    { label: 'Root lead_source: 31', payload: { lead_source: 31 } }
  ]

  process.env.NODE_TLS_REJECT_UNAUTHORIZED = '0'

  for (const item of rootKeys) {
    console.log(`\nTesting root format: ${item.label}`)
    try {
      const response = await fetch(`${apiUrl}/lead/create`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          title: `Test Root: ${item.label}`,
          acf: {
            name: `Test ${item.label}`,
            phone: '0912345678'
          },
          ...item.payload
        })
      })

      const resJson = await response.json()
      console.log('Result:', resJson)

      if (resJson.success && resJson.id) {
        // Wait 4s and fetch it back
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

testRootTaxonomy()
