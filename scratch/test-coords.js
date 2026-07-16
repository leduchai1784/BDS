const { PrismaClient } = require('@prisma/client')
const prisma = new PrismaClient()

async function main() {
  const properties = await prisma.property.findMany({
    select: {
      id: true,
      title: true,
      latitude: true,
      longitude: true,
      priceLabel: true
    }
  })
  console.log(JSON.stringify(properties, null, 2))
}

main().catch(console.error)
