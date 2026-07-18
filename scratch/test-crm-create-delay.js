const dotenv = require('dotenv')
const path = require('path')

dotenv.config({ path: path.join(__dirname, '../.env') })

async function testDelay() {
  const token = process.env.SCRM_API_TOKEN || '01KWKATNQGB5TWXYDPJ671X3X1'
  const apiUrl = process.env.SCRM_API_URL || 'https://sdata.io.vn/wp-json/scrmai/v1'

  process.env.NODE_TLS_REJECT_UNAUTHORIZED = '0'

  console.log('Creating lead with source: 31...')
  const response = await fetch(`${apiUrl}/lead/create`, {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      title: 'Test Lead with ID 31',
      acf: {
        name: 'Test Source 31',
        phone: '0912345678',
        source: 31
      }
    })
  })

  const resJson = await response.json()
  console.log('Create result:', resJson)

  if (resJson.success && resJson.id) {
    const leadId = resJson.id
    console.log(`Waiting 4 seconds for DB to index lead ${leadId}...`)
    await new Promise(r => setTimeout(r, 4000))

    console.log('Fetching leads list with cache: no-store...')
    const fetchRes = await fetch(`${apiUrl}/leads`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      },
      cache: 'no-store'
    })
    const leadsData = await fetchRes.json()
    const savedLead = leadsData.data.find(l => l.id === leadId)
    console.log('Saved Lead ACF:', savedLead ? JSON.stringify(savedLead.acf, null, 2) : 'NOT FOUND IN LIST!')

    // Clean up
    await fetch(`${apiUrl}/lead/delete`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ id: leadId })
    })
    console.log('Deleted temp lead.')
  }
}

testDelay()
