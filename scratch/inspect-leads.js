const dotenv = require('dotenv')
const path = require('path')
dotenv.config({ path: path.join(__dirname, '../.env') })

async function inspectLeads() {
  const token = process.env.SCRM_API_TOKEN || '01KWKATNQGB5TWXYDPJ671X3X1'
  const apiUrl = process.env.SCRM_API_URL || 'https://sdata.io.vn/wp-json/scrmai/v1'
  process.env.NODE_TLS_REJECT_UNAUTHORIZED = '0'

  const res = await fetch(`${apiUrl}/leads`, {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    }
  })
  const data = await res.json()
  if (data.success && Array.isArray(data.data)) {
    // Show newest 3 leads with FULL structure
    const sorted = [...data.data].sort((a, b) => b.id - a.id)
    sorted.slice(0, 3).forEach((lead, i) => {
      console.log(`\n========== Lead #${i+1} (ID: ${lead.id}) ==========`)
      console.log('Title:', lead.title)
      console.log('Created:', lead.created_at)
      console.log('FULL RAW LEAD OBJECT:')
      console.log(JSON.stringify(lead, null, 2))
    })
  }
}
inspectLeads()
