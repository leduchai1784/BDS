const { PrismaClient } = require('@prisma/client')
const prisma = new PrismaClient()

async function main() {
  try {
    console.log('Testing Home Page prisma queries...')

    // 1. Properties
    console.log('Fetching properties...')
    const dbProperties = await prisma.property.findMany({
      where: {
        status: 'approved',
        deletedAt: null
      },
      include: {
        propertyImages: {
          where: { isPrimary: true }
        }
      },
      orderBy: {
        createdAt: 'desc'
      }
    })
    console.log(`Fetched ${dbProperties.length} properties.`)

    // Map properties
    const dbList = dbProperties.map(p => ({
      id: p.id,
      title: p.title,
      price: Number(p.price),
      priceLabel: p.priceLabel,
      area: p.area,
      bedroom: p.bedroom,
      bathroom: p.bathroom,
      floors: p.floors,
      address: p.address,
      district: p.district,
      city: p.city,
      isVip: p.isVip,
      isNew: p.isNew,
      propertyType: p.propertyType,
      imagePath: p.propertyImages?.[0]?.imagePath || null,
      createdAt: p.createdAt
    }))

    // 2. Projects
    console.log('Fetching projects...')
    const projects = await prisma.project.findMany({
      take: 6,
      orderBy: {
        createdAt: 'desc'
      }
    })
    console.log(`Fetched ${projects.length} projects.`)

    const projectList = projects.map(p => ({
      id: Number(p.id),
      title: p.title,
      slug: p.slug,
      description: p.description,
      location: p.location,
      priceRange: p.priceRange,
      status: p.status,
      images: p.images
    }))

    // Try serializing to JSON
    console.log('Attempting to serialize properties list...')
    JSON.stringify(dbList)
    console.log('Successfully serialized properties.')

    console.log('Attempting to serialize projects list...')
    JSON.stringify(projectList)
    console.log('Successfully serialized projects.')

    // 3. NKS properties
    console.log('Fetching NKS properties...')
    const { getNksProperties } = require('../src/lib/nks')
    const nksList = await getNksProperties()
    console.log(`Fetched ${nksList.length} NKS properties.`)

    // Try combining
    const combined = [...dbList, ...nksList]
    console.log(`Combined list size: ${combined.length}`)

    console.log('All homepage DB and NKS queries and serialization passed!')
  } catch (err) {
    console.error('Error during homepage test:', err)
  } finally {
    await prisma.$disconnect()
  }
}

main()
