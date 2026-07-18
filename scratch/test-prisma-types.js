const dotenv = require('dotenv')
const path = require('path')
const { PrismaClient } = require('@prisma/client')

dotenv.config({ path: path.join(__dirname, '../.env') })
const prisma = new PrismaClient()

async function testPrismaTypes() {
  process.env.NODE_TLS_REJECT_UNAUTHORIZED = '0'
  try {
    console.log('Testing Prisma insert with integer addProvince...')
    // lehai17082004 has add_province: 1 (number)
    // Let's try to upsert lehai17082004@gmail.com using the exact mapping of src/lib/nks.ts
    const userEmail = 'lehai17082004@gmail.com'
    
    // Simulate raw mapping from NKS (add_province is a number)
    const data = {
      name: 'Lê Đức Hải',
      nksUserId: '123',
      nksToken: 'test-token',
      addProvince: 1, // Number!
      addDistrict: null,
      addWard: null,
      password: 'test-password-hash'
    }

    await prisma.user.update({
      where: { email: userEmail },
      data: data
    })
    console.log('✅ Successfully updated with number!')
  } catch (err) {
    console.error('❌ Failed to update with number!')
    console.error(err.message)
  } finally {
    await prisma.$disconnect()
  }
}

testPrismaTypes()
