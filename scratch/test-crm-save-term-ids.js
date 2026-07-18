const dotenv = require('dotenv')
const path = require('path')

dotenv.config({ path: path.join(__dirname, '../.env') })

async function testTermIds() {
  const token = process.env.SCRM_API_TOKEN || '01KWKATNQGB5TWXYDPJ671X3X1'
  const apiUrl = process.env.SCRM_API_URL || 'https://sdata.io.vn/wp-json/scrmai/v1'

  // Let's test term IDs from 25 to 45
  process.env.NODE_TLS_REJECT_UNAUTHORIZED = '0'

  for (let id = 25; id <= 45; id++) {
    console.log(`Testing term ID: ${id}`)
    try {
      const response = await fetch(`${apiUrl}/lead/create`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          title: `Test Term ID: ${id}`,
          acf: {
            name: `Test ID ${id}`,
            phone: '0912345678',
            source: id
          }
        })
      })

      const resJson = await response.json()
      if (resJson.success && resJson.id) {
        const fetchRes = await fetch(`${apiUrl}/leads`, {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
          }
        })
        const leadsData = await fetchRes.json()
        const savedLead = leadsData.data.find(l => l.id === resJson.id)
        const savedSource = savedLead?.acf?.source

        if (savedSource && savedSource.id) {
          console.log(`>>> SUCCESS! Term ID ${id} saved as:`, savedSource)
        }

        // Delete the temp lead
        await fetch(`${apiUrl}/lead/delete`, {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ id: resJson.id })
        })
      }
    } catch (e) {
      console.error(`Error for ID ${id}:`, e.message)
    }
  }
}

testTermIds()
