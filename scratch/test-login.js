const dotenv = require('dotenv')
const path = require('path')
const { PrismaClient } = require('@prisma/client')
const bcrypt = require('bcryptjs')
const axios = require('axios')

dotenv.config({ path: path.join(__dirname, '../.env') })
const prisma = new PrismaClient()

async function testUserLogin() {
  const email = 'lehai17082004@gmail.com'
  const password = '12345678'
  process.env.NODE_TLS_REJECT_UNAUTHORIZED = '0'

  console.log(`=== Testing Login for: ${email} ===`)

  // Step 1: Check Local DB
  console.log('\nChecking local database...')
  const localUser = await prisma.user.findUnique({ where: { email } })
  if (localUser) {
    console.log('  Local user found!')
    console.log('  ID:', localUser.id)
    console.log('  Name:', localUser.name)
    console.log('  Role:', localUser.role)
    console.log('  Status:', localUser.status)
    console.log('  Has password hash:', !!localUser.password)
    if (localUser.password) {
      const isMatch = await bcrypt.compare(password, localUser.password)
      console.log('  Password comparison with local hash:', isMatch ? '✅ MATCH' : '❌ NOT MATCH')
    }
  } else {
    console.log('  Local user NOT found in database.')
  }

  // Step 2: Try NKS login
  console.log('\nTrying NKS API login...')
  const nksUrl = process.env.NKS_API_URL || 'https://nks.sdata.io.vn/wp-json/jwt-auth/v1/token'
  try {
    const response = await axios.post(nksUrl, {
      username: email,
      password: password
    }, {
      timeout: 5000
    })
    console.log('  NKS API response success!')
    console.log('  Response keys:', Object.keys(response.data))
  } catch (err) {
    console.log('  NKS API failed!')
    if (err.response) {
      console.log('  Status:', err.response.status)
      console.log('  Response:', JSON.stringify(err.response.data))
    } else {
      console.log('  Error Message:', err.message)
    }
  }

  await prisma.$disconnect()
}

testUserLogin()
