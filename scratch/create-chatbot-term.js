const dotenv = require('dotenv')
const path = require('path')
dotenv.config({ path: path.join(__dirname, '../.env') })

async function createChatbotSourceTerm() {
  const token = process.env.SCRM_API_TOKEN || '01KWKATNQGB5TWXYDPJ671X3X1'
  process.env.NODE_TLS_REJECT_UNAUTHORIZED = '0'
  const headers = {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json'
  }

  // Try via SCRM API endpoints
  const endpoints = [
    { url: 'https://sdata.io.vn/wp-json/scrmai/v1/source/create', body: { title: 'AI Chatbot', slug: 'chatbot' } },
    { url: 'https://sdata.io.vn/wp-json/scrmai/v1/sources/create', body: { title: 'AI Chatbot', slug: 'chatbot' } },
    { url: 'https://sdata.io.vn/wp-json/scrmai/v1/term/create', body: { title: 'AI Chatbot', slug: 'chatbot', taxonomy: 'source' } },
    { url: 'https://sdata.io.vn/wp-json/scrmai/v1/config', body: { action: 'create_source', title: 'AI Chatbot', slug: 'chatbot' } },
  ]

  for (const ep of endpoints) {
    try {
      console.log(`Trying: ${ep.url}`)
      const res = await fetch(ep.url, { method: 'POST', headers, body: JSON.stringify(ep.body) })
      const text = await res.text()
      console.log(`  Status: ${res.status}, Body: ${text.slice(0, 200)}`)
    } catch (e) {
      console.log(`  Error: ${e.message}`)
    }
  }
}

createChatbotSourceTerm()
