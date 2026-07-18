const { PrismaClient } = require('@prisma/client')
const prisma = new PrismaClient()

async function main() {
  const users = await prisma.user.findMany({
    select: {
      id: true,
      name: true,
      email: true,
      role: true,
      status: true
    }
  })
  
  console.log('=== LIST OF USERS ===')
  for (const u of users) {
    console.log(`ID: ${u.id} | Name: ${u.name} | Email: ${u.email} | Role: ${u.role} | Status: ${u.status}`)
  }
}

main()
  .catch(console.error)
  .finally(() => prisma.$disconnect())
