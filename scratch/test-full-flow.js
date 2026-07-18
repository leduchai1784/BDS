const dotenv = require('dotenv')
const path = require('path')
dotenv.config({ path: path.join(__dirname, '../.env') })

async function testFullFlow() {
  const token = process.env.SCRM_API_TOKEN || '01KWKATNQGB5TWXYDPJ671X3X1'
  const apiUrl = process.env.SCRM_API_URL || 'https://sdata.io.vn/wp-json/scrmai/v1'
  process.env.NODE_TLS_REJECT_UNAUTHORIZED = '0'
  const headers = {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json'
  }

  // Simulate exact chatbot flow: create then update with root-level fields
  console.log('Step 1: Creating lead...')
  const createRes = await fetch(`${apiUrl}/lead/create`, {
    method: 'POST',
    headers,
    body: JSON.stringify({ title: 'Nguyen Van Test - 0901234567' })
  })
  const createData = await createRes.json()
  console.log('Create result:', createData)

  if (!createData?.success || !createData?.id) {
    console.log('Create failed!')
    return
  }

  console.log('\nStep 2: Updating lead with root-level fields + source ID 31...')
  const updateRes = await fetch(`${apiUrl}/lead/update`, {
    method: 'POST',
    headers,
    body: JSON.stringify({
      id: createData.id,
      name: 'Nguyen Van Test',
      phone: '0901234567',
      email: 'test@bds.vn',
      zalo: '0901234567',
      demand: 'Tìm thuê căn hộ Quận 10 giá 15 triệu/tháng',
      source: 31,
      note: 'Khách hàng tiềm năng từ AI Chatbot'
    })
  })
  const updateData = await updateRes.json()
  console.log('Update result:', updateData)

  console.log('\nStep 3: Fetching back to verify...')
  await new Promise(r => setTimeout(r, 2000))

  const fetchRes = await fetch(`${apiUrl}/leads`, { method: 'POST', headers })
  const leadsData = await fetchRes.json()
  const lead = leadsData.data.find(l => l.id === createData.id)

  console.log('\n====== VERIFIED LEAD DATA ======')
  console.log(JSON.stringify(lead, null, 2))

  // Check all fields
  const acf = lead?.acf || {}
  console.log('\n====== FIELD CHECK ======')
  console.log('Name:', acf.name || '❌ MISSING')
  console.log('Phone:', acf.phone || '❌ MISSING')
  console.log('Email:', acf.email || '❌ MISSING')
  console.log('Demand:', acf.demand || '❌ MISSING')
  console.log('Note:', acf.note || '❌ MISSING')
  console.log('Source:', acf.source ? `✅ ${JSON.stringify(acf.source)}` : '❌ MISSING')

  // Cleanup
  await fetch(`${apiUrl}/lead/delete`, { method: 'POST', headers, body: JSON.stringify({ id: createData.id }) })
  console.log('\nCleaned up test lead.')
}

testFullFlow()
