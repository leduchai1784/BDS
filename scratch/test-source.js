const dotenv = require('dotenv')
const path = require('path')
dotenv.config({ path: path.join(__dirname, '../.env') })

async function testSourceField() {
  const token = process.env.SCRM_API_TOKEN || '01KWKATNQGB5TWXYDPJ671X3X1'
  const apiUrl = process.env.SCRM_API_URL || 'https://sdata.io.vn/wp-json/scrmai/v1'
  process.env.NODE_TLS_REJECT_UNAUTHORIZED = '0'
  const headers = {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json'
  }

  // Create + Update with source as string
  console.log('Creating lead...')
  const createRes = await fetch(`${apiUrl}/lead/create`, { method: 'POST', headers, body: JSON.stringify({ title: 'Source Test' }) })
  const createData = await createRes.json()
  const id = createData.id

  console.log('Updating with source as string "chatbot"...')
  await fetch(`${apiUrl}/lead/update`, { method: 'POST', headers, body: JSON.stringify({ id, name: 'Source Test', phone: '0912345678', source: 'chatbot' }) })

  await new Promise(r => setTimeout(r, 2000))
  
  const fetchRes = await fetch(`${apiUrl}/leads`, { method: 'POST', headers })
  const leadsData = await fetchRes.json()
  const lead = leadsData.data.find(l => l.id === id)
  console.log('\nFull lead with source:')
  console.log(JSON.stringify(lead, null, 2))

  // Cleanup
  await fetch(`${apiUrl}/lead/delete`, { method: 'POST', headers, body: JSON.stringify({ id }) })
  console.log('Cleaned up.')
}

testSourceField()
