import { prisma } from '@/lib/prisma'
import { getNksProperties } from '@/lib/nks'
import { buildPrismaFilters } from '@/lib/propertyFilters'
import MapPageClient from '@/components/map/MapPageClient'

export const dynamic = 'force-dynamic'

interface MapPageProps {
  searchParams: Promise<Record<string, string | string[] | undefined>>
}

export default async function MapPage({ searchParams }: MapPageProps) {
  const resolvedParams = await searchParams

  const keyword = typeof resolvedParams.keyword === 'string' ? resolvedParams.keyword : ''
  const purpose = typeof resolvedParams.purpose === 'string' ? resolvedParams.purpose : ''
  const propertyType = typeof resolvedParams.property_type === 'string' ? resolvedParams.property_type : ''
  const province = typeof resolvedParams.province === 'string' ? resolvedParams.province : ''
  const district = typeof resolvedParams.district === 'string' ? resolvedParams.district : ''
  const ward = typeof resolvedParams.ward === 'string' ? resolvedParams.ward : ''
  const targetId = typeof resolvedParams.id === 'string' ? resolvedParams.id : ''

  // 1. Fetch initially filtered DB properties
  const dbWhere = buildPrismaFilters({
    keyword,
    purpose,
    property_type: propertyType,
    province,
    district,
    ward
  })

  // If a targetId is provided, we should ensure it gets loaded even if it doesn't match current filters
  let dbPropertiesRaw = await prisma.property.findMany({
    where: dbWhere,
    include: {
      propertyImages: {
        where: { isPrimary: true }
      }
    }
  })

  // If targetId is provided and not in the list, fetch and append it
  if (targetId && !dbPropertiesRaw.some(p => p.id === targetId)) {
    // Basic UUID validation before calling db
    const uuidRegex = /^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i
    if (uuidRegex.test(targetId)) {
      const targetProp = await prisma.property.findUnique({
        where: { id: targetId },
        include: {
          propertyImages: {
            where: { isPrimary: true }
          }
        }
      })
      if (targetProp) {
        dbPropertiesRaw = [targetProp, ...dbPropertiesRaw]
      }
    }
  }

  // Map DB properties
  const dbList = dbPropertiesRaw.map(p => ({
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
    latitude: p.latitude,
    longitude: p.longitude,
    isVip: p.isVip,
    isNew: p.isNew,
    propertyType: p.propertyType,
    imagePath: p.propertyImages?.[0]?.imagePath || null
  }))

  // 2. Fetch and filter NKS properties
  const nksPropertiesAll = await getNksProperties()
  const nksFiltered = nksPropertiesAll.filter(item => {
    // Keyword match
    if (keyword) {
      const kw = keyword.toLowerCase()
      const matches = 
        item.title.toLowerCase().includes(kw) ||
        item.address.toLowerCase().includes(kw) ||
        item.district.toLowerCase().includes(kw) ||
        item.city.toLowerCase().includes(kw)
      if (!matches) return false
    }

    // Purpose match
    if (purpose) {
      const isRent = item.priceLabel.toLowerCase().includes('tháng') || item.priceLabel.toLowerCase().includes('thang')
      const itemPurpose = isRent ? 'rent' : 'sale'
      if (itemPurpose !== purpose) return false
    }

    // Property Type match
    if (propertyType) {
      if (item.propertyType !== propertyType) return false
    }

    // Province / City match
    if (province) {
      const cleanP = province.replace(/Thành phố|Tỉnh/g, '').trim().toLowerCase()
      if (!item.city.toLowerCase().includes(cleanP)) return false
    }

    // District match
    if (district) {
      const cleanD = district.replace(/Quận|Huyện|Thị xã|Thành phố/g, '').trim().toLowerCase()
      if (!item.district.toLowerCase().includes(cleanD) && !item.address.toLowerCase().includes(cleanD)) return false
    }

    // Ward match
    if (ward) {
      const cleanW = ward.replace(/Phường|Xã|Thị trấn/g, '').trim().toLowerCase()
      if (!item.address.toLowerCase().includes(cleanW)) return false
    }

    return true
  })

  // 3. Merge
  const combinedList = [...dbList, ...nksFiltered]

  return (
    <MapPageClient initialProperties={combinedList} />
  )
}
