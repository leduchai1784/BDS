const dotenv = require('dotenv')
const path = require('path')

dotenv.config({ path: path.join(__dirname, '../.env') })

async function testUpdateDeleteLead() {
  const token = process.env.SCRM_API_TOKEN
  const apiUrl = process.env.SCRM_API_URL || 'https://sdata.io.vn/wp-json/scrmai/v1'

  // First create a temp lead
  console.log('Creating temp lead...')
  const createResponse = await fetch(`${apiUrl}/lead/create`, {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      title: 'Temp Lead for update test',
      acf: {
        name: 'Temp Name',
        phone: '0988888888'
      }
    })
  })

  const createResult = await createResponse.json()
  console.log('Created:', createResult)

  if (!createResult.success || !createResult.id) {
    console.log('Failed to create lead for testing')
    return
  }

  const leadId = createResult.id

  // Test Update
  console.log(`\nTesting update for lead ${leadId}...`)
  const updateResponse = await fetch(`${apiUrl}/lead/update`, {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      id: leadId,
      acf: {
        name: 'Updated Temp Name',
        phone: '0988888888',
        note: 'Đã cập nhật trạng thái chăm sóc'
      }
    })
  })

  console.log('Update Status:', updateResponse.status)
  const updateText = await updateResponse.text()
  console.log('Update Response:', updateText)

  // Test Delete
  console.log(`\nTesting delete for lead ${leadId}...`)
  const deleteResponse = await fetch(`${apiUrl}/lead/delete`, {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      id: leadId
    })
  })

  console.log('Delete Status:', deleteResponse.status)
  const deleteText = await deleteResponse.text()
  console.log('Delete Response:', deleteText)
}

testUpdateDeleteLead()
