const dotenv = require('dotenv')
const path = require('path')
dotenv.config({ path: path.join(__dirname, '../.env') })

async function testSourceFormats() {
  const token = process.env.SCRM_API_TOKEN || '01KWKATNQGB5TWXYDPJ671X3X1'
  const apiUrl = process.env.SCRM_API_URL || 'https://sdata.io.vn/wp-json/scrmai/v1'
  process.env.NODE_TLS_REJECT_UNAUTHORIZED = '0'
  const headers = {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json'
  }

  // From the existing data, Lead #198 has source: { id: 31, title: 'Website', slug: 'website' }
  // So 31 is the ID for "Website" source term
  // Let's try sending source as an integer ID

  const formats = [
    { label: 'source as integer (31)', data: { source: 31 } },
    { label: 'source as string ID "31"', data: { source: '31' } },
    { label: 'source as object {id:31}', data: { source: { id: 31 } } },
    { label: 'source as slug "website"', data: { source: 'website' } },
  ]

  for (const format of formats) {
    console.log(`\nTesting: ${format.label}...`)
    const createRes = await fetch(`${apiUrl}/lead/create`, { method: 'POST', headers, body: JSON.stringify({ title: 'Source Format Test' }) })
    const createData = await createRes.json()
    const id = createData.id

    await fetch(`${apiUrl}/lead/update`, { method: 'POST', headers, body: JSON.stringify({ id, name: 'Test', phone: '0999888777', ...format.data }) })

    await new Promise(r => setTimeout(r, 1500))
    
    const fetchRes = await fetch(`${apiUrl}/leads`, { method: 'POST', headers })
    const leadsData = await fetchRes.json()
    const lead = leadsData.data.find(l => l.id === id)
    console.log('Source value returned:', JSON.stringify(lead?.acf?.source))

    await fetch(`${apiUrl}/lead/delete`, { method: 'POST', headers, body: JSON.stringify({ id }) })
  }

  console.log('\nDone.')
}

testSourceFormats()
