const dotenv = require('dotenv')
const path = require('path')

dotenv.config({ path: path.join(__dirname, '../.env') })

async function testWpTerms() {
  const token = process.env.SCRM_API_TOKEN || '01KWKATNQGB5TWXYDPJ671X3X1'
  const rootUrl = 'https://sdata.io.vn/wp-json/wp/v2'

  process.env.NODE_TLS_REJECT_UNAUTHORIZED = '0'

  // We will try to fetch from various possible taxonomy endpoints:
  const endpoints = [
    'source',
    'lead_source',
    'lead-source',
    'tags',
    'categories'
  ]

  for (const ep of endpoints) {
    console.log(`\nFetching from: ${rootUrl}/${ep}`)
    try {
      const res = await fetch(`${rootUrl}/${ep}`, {
        headers: {
          'Authorization': `Bearer ${token}`
        }
      })
      console.log('Status:', res.status)
      if (res.ok) {
        const data = await res.json()
        if (Array.isArray(data)) {
          console.log(`Found ${data.length} terms:`)
          data.forEach(t => console.log(`- ID: ${t.id}, Name: "${t.name}", Slug: "${t.slug}"`))
        } else {
          console.log('Response is not an array:', JSON.stringify(data).substring(0, 200))
        }
      } else {
        const text = await res.text()
        console.log('Error body:', text.substring(0, 200))
      }
    } catch (e) {
      console.error('Error:', e.message)
    }
  }
}

testWpTerms()
