const dotenv = require('dotenv')
const path = require('path')
dotenv.config({ path: path.join(__dirname, '../.env') })

async function testFieldFormats() {
  const token = process.env.SCRM_API_TOKEN || '01KWKATNQGB5TWXYDPJ671X3X1'
  const apiUrl = process.env.SCRM_API_URL || 'https://sdata.io.vn/wp-json/scrmai/v1'
  process.env.NODE_TLS_REJECT_UNAUTHORIZED = '0'

  // Test 1: Send fields at root level (not nested in acf)
  console.log('Test 1: Sending fields at root level...')
  const res1 = await fetch(`${apiUrl}/lead/create`, {
    method: 'POST',
    headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' },
    body: JSON.stringify({
      title: 'Root Level Test - 0909090909',
      name: 'Root Level Test',
      phone: '0909090909',
      email: 'root@test.com',
      demand: 'Tìm thuê căn hộ test',
      note: 'Test root level fields',
      source: 'chatbot'
    })
  })
  const data1 = await res1.json()
  console.log('Result:', data1)
  const id1 = data1.id

  // Test 2: Send fields in 'fields' key
  console.log('\nTest 2: Sending fields in "fields" key...')
  const res2 = await fetch(`${apiUrl}/lead/create`, {
    method: 'POST',
    headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' },
    body: JSON.stringify({
      title: 'Fields Key Test - 0808080808',
      fields: {
        name: 'Fields Key Test',
        phone: '0808080808',
        email: 'fields@test.com',
        demand: 'Tìm thuê căn hộ test 2',
        note: 'Test fields key'
      }
    })
  })
  const data2 = await res2.json()
  console.log('Result:', data2)
  const id2 = data2.id

  // Test 3: Update using fields at root level
  if (id1) {
    console.log('\nTest 3: Updating lead using root-level fields...')
    const res3 = await fetch(`${apiUrl}/lead/update`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' },
      body: JSON.stringify({
        id: id1,
        title: 'Root Level Test Updated',
        name: 'Root Updated',
        phone: '0909090909',
        email: 'root-updated@test.com',
        demand: 'Updated demand',
        note: 'Updated note'
      })
    })
    const data3 = await res3.json()
    console.log('Result:', data3)
  }

  // Test 4: Update using 'fields' key
  if (id2) {
    console.log('\nTest 4: Updating lead using "fields" key...')
    const res4 = await fetch(`${apiUrl}/lead/update`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' },
      body: JSON.stringify({
        id: id2,
        fields: {
          name: 'Fields Updated',
          phone: '0808080808',
          email: 'fields-updated@test.com',
          demand: 'Updated demand 2'
        }
      })
    })
    const data4 = await res4.json()
    console.log('Result:', data4)
  }

  // Fetch back to verify
  console.log('\nWaiting 3s then fetching back...')
  await new Promise(r => setTimeout(r, 3000))
  const fetchRes = await fetch(`${apiUrl}/leads`, {
    method: 'POST',
    headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' }
  })
  const leadsData = await fetchRes.json()
  if (id1) {
    const lead1 = leadsData.data.find(l => l.id === id1)
    console.log('\nLead 1 (root level fields):')
    console.log(JSON.stringify(lead1, null, 2))
  }
  if (id2) {
    const lead2 = leadsData.data.find(l => l.id === id2)
    console.log('\nLead 2 (fields key):')
    console.log(JSON.stringify(lead2, null, 2))
  }

  // Cleanup
  if (id1) await fetch(`${apiUrl}/lead/delete`, { method: 'POST', headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' }, body: JSON.stringify({ id: id1 }) })
  if (id2) await fetch(`${apiUrl}/lead/delete`, { method: 'POST', headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' }, body: JSON.stringify({ id: id2 }) })
  console.log('\nCleaned up test leads.')
}

testFieldFormats()
