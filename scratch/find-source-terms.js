const dotenv = require('dotenv')
const path = require('path')
dotenv.config({ path: path.join(__dirname, '../.env') })

async function findSourceTerms() {
  const token = process.env.SCRM_API_TOKEN || '01KWKATNQGB5TWXYDPJ671X3X1'
  process.env.NODE_TLS_REJECT_UNAUTHORIZED = '0'

  // Try WP REST API to list source taxonomy terms
  const urls = [
    'https://sdata.io.vn/wp-json/wp/v2/source',
    'https://sdata.io.vn/wp-json/wp/v2/sources',
    'https://sdata.io.vn/wp-json/wp/v2/lead_source',
    'https://sdata.io.vn/wp-json/wp/v2/lead-source',
    'https://sdata.io.vn/wp-json/wp/v2/scrm_source',
    'https://sdata.io.vn/wp-json/wp/v2/nguon',
    'https://sdata.io.vn/wp-json/wp/v2/taxonomies',
  ]

  for (const url of urls) {
    try {
      const res = await fetch(url, {
        headers: { 'Authorization': `Bearer ${token}` }
      })
      if (res.ok) {
        const data = await res.json()
        console.log(`\n✅ ${url}`)
        console.log(JSON.stringify(data, null, 2).slice(0, 1000))
      } else {
        console.log(`❌ ${url} - ${res.status}`)
      }
    } catch (e) {
      console.log(`❌ ${url} - Error`)
    }
  }

  // Also try brute-force: set source to various IDs and see which ones exist
  console.log('\n\nBrute-force source IDs:')
  const headers = { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' }
  const createRes = await fetch(`https://sdata.io.vn/wp-json/scrmai/v1/lead/create`, { method: 'POST', headers, body: JSON.stringify({ title: 'Source Brute Test' }) })
  const createData = await createRes.json()
  const id = createData.id

  for (const sourceId of [30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40]) {
    await fetch(`https://sdata.io.vn/wp-json/scrmai/v1/lead/update`, { method: 'POST', headers, body: JSON.stringify({ id, source: sourceId }) })
    await new Promise(r => setTimeout(r, 500))
    const fetchRes = await fetch(`https://sdata.io.vn/wp-json/scrmai/v1/leads`, { method: 'POST', headers })
    const leadsData = await fetchRes.json()
    const lead = leadsData.data.find(l => l.id === id)
    const src = lead?.acf?.source
    if (src) {
      console.log(`  Source ID ${sourceId}: ${JSON.stringify(src)}`)
    }
  }

  await fetch(`https://sdata.io.vn/wp-json/scrmai/v1/lead/delete`, { method: 'POST', headers, body: JSON.stringify({ id }) })
  console.log('\nDone.')
}

findSourceTerms()
