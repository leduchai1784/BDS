const { PrismaClient } = require('@prisma/client')
const prisma = new PrismaClient()

async function test() {
  try {
    console.log('Testing appointment creation in Prisma...')
    const prop = await prisma.property.findFirst()
    if (!prop) {
      console.log('No properties found to test with.')
      return
    }

    const app = await prisma.appointment.create({
      data: {
        propertyId: prop.id,
        name: 'Test Tenant',
        phone: '0987654321',
        email: 'test@bdsrental.vn',
        date: new Date('2026-07-20'),
        time: new Date('1970-01-01T09:30:00Z'),
        message: 'Hello test',
        status: 'approved'
      }
    })
    console.log('Successfully created test appointment with status approved:', app.id)

  } catch (err) {
    console.error('Crash error:', err)
  } finally {
    await prisma.$disconnect()
  }
}

test()
