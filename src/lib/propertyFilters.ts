import { Prisma } from '@prisma/client'

/**
 * Port from Property::scopeFilter()
 * Build a dynamic Prisma where clause from filter inputs
 */
export function buildPrismaFilters(filters: Record<string, any>): Prisma.PropertyWhereInput {
  const where: Prisma.PropertyWhereInput = {
    deletedAt: null // Soft-deleted properties are ignored by default
  }

  // 1. Approved status only (default filter)
  if (!filters.ignore_status) {
    where.status = 'approved'
  }

  // 2. Keyword search
  const keyword = filters.keyword || filters.search
  if (keyword && typeof keyword === 'string' && keyword.trim() !== '') {
    const cleanKw = keyword.trim()
    where.OR = [
      { title: { contains: cleanKw, mode: 'insensitive' } },
      { address: { contains: cleanKw, mode: 'insensitive' } },
      { description: { contains: cleanKw, mode: 'insensitive' } },
      { district: { contains: cleanKw, mode: 'insensitive' } },
      { province: { contains: cleanKw, mode: 'insensitive' } }
    ]
  }

  // 3. Transaction type (purpose: rent/sale)
  const transactionType = filters.transaction_type || filters.purpose
  if (transactionType && typeof transactionType === 'string' && transactionType.trim() !== '') {
    where.transactionType = transactionType.trim()
  }

  // 4. Property type (type)
  const propTypesRaw = filters.property_type || filters.type
  if (propTypesRaw) {
    const types = Array.isArray(propTypesRaw) 
      ? propTypesRaw 
      : typeof propTypesRaw === 'string'
        ? propTypesRaw.split(',').map(s => s.trim())
        : []
    const cleanTypes = types.filter(Boolean)
    if (cleanTypes.length > 0) {
      where.propertyType = { in: cleanTypes }
    }
  }

  // 5. Province / City
  const province = filters.province || filters.city
  if (province && typeof province === 'string' && province.trim() !== '') {
    const cleanP = province.replace(/Thành phố|Tỉnh/g, '').trim()
    where.province = { contains: cleanP, mode: 'insensitive' }
  }

  // 6. District
  const districtRaw = filters.district
  if (districtRaw) {
    const districts = Array.isArray(districtRaw)
      ? districtRaw
      : typeof districtRaw === 'string'
        ? districtRaw.split(',').map(s => s.trim())
        : []
    const cleanDistricts = districts.filter(Boolean)
    if (cleanDistricts.length > 0) {
      const districtConditions = cleanDistricts.map(d => {
        const cleanD = d.replace(/Quận|Huyện|Thị xã|Thành phố/g, '').trim()
        return {
          OR: [
            { district: { contains: cleanD, mode: 'insensitive' } },
            { address: { contains: cleanD, mode: 'insensitive' } }
          ]
        }
      })
      where.AND = where.AND 
        ? [...(where.AND as any), { OR: districtConditions }]
        : [{ OR: districtConditions }]
    }
  }

  // 7. Ward
  const ward = filters.ward
  if (ward && typeof ward === 'string' && ward.trim() !== '') {
    const cleanW = ward.replace(/Phường|Xã|Thị trấn/g, '').trim()
    where.OR = where.OR 
      ? [...(where.OR as any[]), { ward: { contains: cleanW, mode: 'insensitive' } }, { address: { contains: cleanW, mode: 'insensitive' } }]
      : [{ ward: { contains: cleanW, mode: 'insensitive' } }, { address: { contains: cleanW, mode: 'insensitive' } }]
  }

  // 8. Price filtering
  const price = filters.price
  if (price && typeof price === 'string') {
    if (price === 'under_3') {
      where.price = { lt: 3000000 }
    } else if (price === '3_5') {
      where.price = { gte: 3000000, lte: 5000000 }
    } else if (price === '5_10') {
      where.price = { gte: 5000000, lte: 10000000 }
    } else if (price === '10_20') {
      where.price = { gte: 10000000, lte: 20000000 }
    } else if (price === 'above_20') {
      where.price = { gt: 20000000 }
    } else if (price === 'under_1b') {
      where.price = { lt: 1000000000 }
    } else if (price === '1b_3b') {
      where.price = { gte: 1000000000, lte: 3000000000 }
    } else if (price === '3b_5b') {
      where.price = { gte: 3000000000, lte: 5000000000 }
    } else if (price === '5b_10b') {
      where.price = { gte: 5000000000, lte: 10000000000 }
    } else if (price === 'above_10b') {
      where.price = { gt: 10000000000 }
    }
  }

  // 9. Area filtering
  const area = filters.area
  if (area && typeof area === 'string') {
    if (area === 'under_30') {
      where.area = { lt: 30 }
    } else if (area === '30_50') {
      where.area = { gte: 30, lte: 50 }
    } else if (area === '50_80') {
      where.area = { gte: 50, lte: 80 }
    } else if (area === '80_120') {
      where.area = { gte: 80, lte: 120 }
    } else if (area === 'above_120') {
      where.area = { gt: 120 }
    }
  }

  // 10. Bedrooms
  const bedrooms = filters.bedrooms || filters.bedroom
  if (bedrooms) {
    const numBed = parseInt(bedrooms, 10)
    if (!isNaN(numBed)) {
      where.bedroom = { gte: numBed }
    }
  }

  // 11. Bathrooms
  const bathrooms = filters.bathrooms || filters.bathroom
  if (bathrooms) {
    const numBath = parseInt(bathrooms, 10)
    if (!isNaN(numBath)) {
      where.bathroom = { gte: numBath }
    }
  }

  // 12. Furniture
  const furniture = filters.furniture
  if (furniture && typeof furniture === 'string') {
    if (furniture === 'full') {
      where.OR = where.OR 
        ? [...(where.OR as any[]), { furniture: { contains: 'đầy đủ', mode: 'insensitive' } }, { furniture: { contains: 'full', mode: 'insensitive' } }]
        : [{ furniture: { contains: 'đầy đủ', mode: 'insensitive' } }, { furniture: { contains: 'full', mode: 'insensitive' } }]
    } else if (furniture === 'basic') {
      where.furniture = { contains: 'cơ bản', mode: 'insensitive' }
    } else if (furniture === 'none') {
      where.OR = where.OR 
        ? [...(where.OR as any[]), { furniture: { contains: 'không', mode: 'insensitive' } }, { furniture: { contains: 'trống', mode: 'insensitive' } }, { furniture: null }]
        : [{ furniture: { contains: 'không', mode: 'insensitive' } }, { furniture: { contains: 'trống', mode: 'insensitive' } }, { furniture: null }]
    }
  }

  return where
}

/**
 * Port from Property::scopeSort()
 * Build a Prisma orderBy statement from a sort key string
 */
export function buildPrismaOrderBy(sortBy: string): Prisma.PropertyOrderByWithRelationInput {
  switch (sortBy) {
    case 'price_asc':
      return { price: 'asc' }
    case 'price_desc':
      return { price: 'desc' }
    case 'area_asc':
      return { area: 'asc' }
    case 'area_desc':
      return { area: 'desc' }
    case 'latest':
    default:
      return { createdAt: 'desc' }
  }
}
