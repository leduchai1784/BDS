const dotenv = require('dotenv')
const path = require('path')
const { PrismaClient } = require('@prisma/client')
const { randomUUID } = require('crypto')

dotenv.config({ path: path.join(__dirname, '../.env') })
const prisma = new PrismaClient()

async function testCreateProperty() {
  process.env.NODE_TLS_REJECT_UNAUTHORIZED = '0'
  try {
    console.log('Simulating property creation matching owner post logic...')
    
    // Find a valid owner user (role = owner or tenant, let's use user 50165 we just created)
    let owner = await prisma.user.findFirst({ where: { email: 'lehai17082004@gmail.com' } })
    if (!owner) {
      console.log('Owner user not found, using first user from DB.')
      owner = await prisma.user.findFirst()
    }

    if (!owner) {
      console.log('No user in database, cannot test.')
      return
    }

    console.log(`Using owner: ${owner.name} (ID: ${owner.id.toString()})`)

    // Step 1: Detect category
    const property_type = 'Căn hộ chung cư'
    let cat = await prisma.category.findFirst({
      where: {
        OR: [
          { name: { contains: property_type } },
          { name: { equals: property_type } }
        ]
      }
    })
    
    let finalCategoryId = cat ? cat.id : null
    if (!finalCategoryId) {
      const fallbackCat = await prisma.category.findFirst()
      finalCategoryId = fallbackCat ? fallbackCat.id : 1n
    }

    // Step 2: Insert property
    const title = 'Căn Hộ Mẫu Test Đăng Tin Chủ Nhà'
    const slug = 'can-ho-mau-test-dang-tin-chu-nha-' + Date.now()

    const property = await prisma.property.create({
      data: {
        ownerId: owner.id,
        categoryId: finalCategoryId,
        title,
        slug,
        description: 'Mô tả chi tiết căn hộ mẫu test đăng tin chủ nhà.',
        price: BigInt(15000000),
        priceLabel: '15 triệu/tháng',
        area: 75,
        bedroom: 2,
        bathroom: 2,
        address: 'Tòa Park 2, Vinhomes Symphony',
        ward: 'Phúc Lợi',
        district: 'Long Biên',
        city: 'Hà Nội',
        latitude: 21.0435,
        longitude: 105.9123,
        phone: owner.phone || '0977.758.217',
        status: 'approved',
        direction: 'Đông Nam',
        furniture: 'Đầy đủ đồ cao cấp',
        legal: 'Sổ hồng',
        deposit: BigInt(30000000),
        leaseTerm: 'Tối thiểu 1 năm',
        frontage: 5.5, // Float/Number
        roadWidth: 10.0, // Float/Number
        floors: 15,
        propertyType: property_type
      }
    })

    console.log('✅ Property record created successfully! ID:', property.id)

    // Step 3: Insert image
    const imagesData = [{
      id: randomUUID(),
      propertyId: property.id,
      imagePath: 'https://res.cloudinary.com/dj8t18pke/image/upload/v1782101764/ewjyvlwq88ixefrpstmu.jpg',
      isPrimary: true
    }]

    await prisma.propertyImage.createMany({
      data: imagesData
    })
    console.log('✅ Property images created successfully!')

    // Clean up
    await prisma.property.delete({ where: { id: property.id } })
    console.log('🗑️ Cleaned up test property.')
  } catch (err) {
    console.error('❌ Failed to run create property logic!')
    console.error(err.stack || err.message)
  } finally {
    await prisma.$disconnect()
  }
}

testCreateProperty()
