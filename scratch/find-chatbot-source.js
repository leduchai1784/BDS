const dotenv = require('dotenv')
const path = require('path')
dotenv.config({ path: path.join(__dirname, '../.env') })

async function findOrCreateChatbotSource() {
  const token = process.env.SCRM_API_TOKEN || '01KWKATNQGB5TWXYDPJ671X3X1'
  const apiUrl = process.env.SCRM_API_URL || 'https://sdata.io.vn/wp-json/scrmai/v1'
  process.env.NODE_TLS_REJECT_UNAUTHORIZED = '0'
  const headers = {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json'
  }

  // Brute-force search for chatbot source term (extend range to 100)
  console.log('Searching for existing Chatbot source term (IDs 30-100)...')
  const createRes = await fetch(`${apiUrl}/lead/create`, { method: 'POST', headers, body: JSON.stringify({ title: 'Source Scan' }) })
  const createData = await createRes.json()
  const id = createData.id

  const foundSources = []
  for (let sourceId = 25; sourceId <= 100; sourceId++) {
    await fetch(`${apiUrl}/lead/update`, { method: 'POST', headers, body: JSON.stringify({ id, source: sourceId }) })
    await new Promise(r => setTimeout(r, 300))
    const fetchRes = await fetch(`${apiUrl}/leads`, { method: 'POST', headers })
    const leadsData = await fetchRes.json()
    const lead = leadsData.data.find(l => l.id === id)
    const src = lead?.acf?.source
    if (src) {
      foundSources.push({ id: sourceId, data: src })
      console.log(`  ✅ Source ID ${sourceId}: ${JSON.stringify(src)}`)
    }
  }

  await fetch(`${apiUrl}/lead/delete`, { method: 'POST', headers, body: JSON.stringify({ id }) })

  console.log('\n\nAll found source terms:')
  foundSources.forEach(s => console.log(`  ID: ${s.id} => ${s.data.title} (slug: ${s.data.slug})`))

  // Check if 'chatbot' slug exists
  const chatbotTerm = foundSources.find(s => s.data.slug === 'chatbot' || s.data.title.toLowerCase().includes('chatbot'))
  if (chatbotTerm) {
    console.log(`\n🎉 Found Chatbot term! ID: ${chatbotTerm.id}`)
  } else {
    console.log('\n⚠️ No Chatbot source term found in IDs 25-100.')
    console.log('Available terms:', foundSources.map(s => `${s.data.title} (${s.id})`).join(', '))
  }
}

findOrCreateChatbotSource()
