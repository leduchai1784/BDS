const dotenv = require('dotenv')
const path = require('path')
dotenv.config({ path: path.join(__dirname, '../.env') })

async function testCreateThenUpdate() {
  const token = process.env.SCRM_API_TOKEN || '01KWKATNQGB5TWXYDPJ671X3X1'
  const apiUrl = process.env.SCRM_API_URL || 'https://sdata.io.vn/wp-json/scrmai/v1'
  process.env.NODE_TLS_REJECT_UNAUTHORIZED = '0'

  // Step 1: Create lead with title only
  console.log('Step 1: Creating lead...')
  const createRes = await fetch(`${apiUrl}/lead/create`, {
    method: 'POST',
    headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' },
    body: JSON.stringify({
      title: 'Nguyen Test - 0901234567',
      acf: {
        name: 'Nguyen Test',
        phone: '0901234567',
        email: 'test@example.com',
        zalo: '0901234567',
        demand: 'Tìm thuê căn hộ Quận 10',
        note: 'Khách hàng tiềm năng từ AI Chatbot'
      }
    })
  })
  const createData = await createRes.json()
  console.log('Create result:', createData)

  if (!createData.success || !createData.id) {
    console.log('Create failed, aborting.')
    return
  }

  const leadId = createData.id

  // Step 2: Immediately update the lead ACF via /lead/update
  console.log('\nStep 2: Updating lead ACF via /lead/update...')
  const updateRes = await fetch(`${apiUrl}/lead/update`, {
    method: 'POST',
    headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' },
    body: JSON.stringify({
      id: leadId,
      acf: {
        name: 'Nguyen Test',
        phone: '0901234567',
        email: 'test@example.com',
        zalo: '0901234567',
        demand: 'Tìm thuê căn hộ Quận 10',
        note: 'Khách hàng tiềm năng từ AI Chatbot'
      }
    })
  })
  const updateData = await updateRes.json()
  console.log('Update result:', updateData)

  // Step 3: Wait and fetch back to verify
  console.log('\nStep 3: Waiting 3s then fetching back...')
  await new Promise(r => setTimeout(r, 3000))
  
  const fetchRes = await fetch(`${apiUrl}/leads`, {
    method: 'POST',
    headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' },
    cache: 'no-store'
  })
  const leadsData = await fetchRes.json()
  const savedLead = leadsData.data.find(l => l.id === leadId)
  console.log('\nSaved Lead FULL object:')
  console.log(JSON.stringify(savedLead, null, 2))

  // Cleanup
  await fetch(`${apiUrl}/lead/delete`, {
    method: 'POST',
    headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' },
    body: JSON.stringify({ id: leadId })
  })
  console.log('\nDeleted test lead.')
}

testCreateThenUpdate()
