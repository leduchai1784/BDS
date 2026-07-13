import { prisma } from '@/lib/prisma'
import { getNksProperties } from '@/lib/nks'
import { buildPrismaFilters } from '@/lib/propertyFilters'
import PropertyCard from '@/components/property/PropertyCard'
import FilterPanel from '@/components/property/FilterPanel'
import SortSelector from '@/components/property/SortSelector'
import Pagination from '@/components/property/Pagination'
import Link from 'next/link'

export const dynamic = 'force-dynamic'

interface ListingsPageProps {
  searchParams: Promise<Record<string, string | string[] | undefined>>
}

export default async function ListingsPage({ searchParams }: ListingsPageProps) {
  const resolvedParams = await searchParams
  
  // Extract query filters
  const page = Number(resolvedParams.page || 1)
  const limit = 12
  const sort = String(resolvedParams.sort || 'latest')
  
  const keyword = typeof resolvedParams.keyword === 'string' ? resolvedParams.keyword : ''
  const purpose = typeof resolvedParams.purpose === 'string' ? resolvedParams.purpose : ''
  const propertyType = typeof resolvedParams.property_type === 'string' ? resolvedParams.property_type : ''
  const province = typeof resolvedParams.province === 'string' ? resolvedParams.province : ''
  const district = typeof resolvedParams.district === 'string' ? resolvedParams.district : ''
  const ward = typeof resolvedParams.ward === 'string' ? resolvedParams.ward : ''
  const price = typeof resolvedParams.price === 'string' ? resolvedParams.price : ''
  const area = typeof resolvedParams.area === 'string' ? resolvedParams.area : ''
  const bedrooms = typeof resolvedParams.bedrooms === 'string' ? resolvedParams.bedrooms : ''
  const bathrooms = typeof resolvedParams.bathrooms === 'string' ? resolvedParams.bathrooms : ''
  const furniture = typeof resolvedParams.furniture === 'string' ? resolvedParams.furniture : ''

  // 1. Fetch filtered Database properties
  const dbWhere = buildPrismaFilters({
    keyword,
    purpose,
    property_type: propertyType,
    province,
    district,
    ward,
    price,
    area,
    bedrooms,
    bathrooms,
    furniture
  })

  const dbPropertiesRaw = await prisma.property.findMany({
    where: dbWhere,
    include: {
      propertyImages: {
        where: { isPrimary: true }
      }
    }
  })

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
    isVip: p.isVip,
    isNew: p.isNew,
    propertyType: p.propertyType,
    imagePath: p.propertyImages?.[0]?.imagePath || null,
    createdAt: p.createdAt,
    transactionType: p.transactionType
  }))

  // 2. Fetch and filter NKS API properties in memory
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
      // NKS maps to transactionType
      if (item.transactionType !== purpose) {
        // Wait, in getNksProperties, did we map it? Let's check:
        // getNksProperties maps priceLabel or checks rentprice vs price.
        // We set propertyType, isVip, isNew, address, district, city.
        // Let's deduce transaction type: if priceLabel includes 'tháng', it's rent.
        const isRent = item.priceLabel.toLowerCase().includes('tháng') || item.priceLabel.toLowerCase().includes('thang')
        const itemPurpose = isRent ? 'rent' : 'sale'
        if (itemPurpose !== purpose) return false
      }
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

    // Price match
    if (price) {
      const pVal = item.price
      if (price === 'under_3' && pVal >= 3000000) return false
      if (price === '3_5' && (pVal < 3000000 || pVal > 5000000)) return false
      if (price === '5_10' && (pVal < 5000000 || pVal > 10000000)) return false
      if (price === '10_20' && (pVal < 10000000 || pVal > 20000000)) return false
      if (price === 'above_20' && pVal <= 20000000) return false
      if (price === 'under_1b' && pVal >= 1000000000) return false
      if (price === '1b_3b' && (pVal < 1000000000 || pVal > 3000000000)) return false
      if (price === '3b_5b' && (pVal < 3000000000 || pVal > 5000000000)) return false
      if (price === '5b_10b' && (pVal < 5000000000 || pVal > 10000000000)) return false
      if (price === 'above_10b' && pVal <= 10000000000) return false
    }

    // Area match
    if (area) {
      const aVal = item.area
      if (area === 'under_30' && aVal >= 30) return false
      if (area === '30_50' && (aVal < 30 || aVal > 50)) return false
      if (area === '50_80' && (aVal < 50 || aVal > 80)) return false
      if (area === '80_120' && (aVal < 80 || aVal > 120)) return false
      if (area === 'above_120' && aVal <= 120) return false
    }

    // Bedrooms match
    if (bedrooms) {
      const bVal = parseInt(bedrooms, 10)
      if (!isNaN(bVal) && item.bedroom < bVal) return false
    }

    // Bathrooms match
    if (bathrooms) {
      const btVal = parseInt(bathrooms, 10)
      if (!isNaN(btVal) && item.bathroom < btVal) return false
    }

    return true
  })

  // 3. Merge Database and API lists
  const combined = [...dbList, ...nksFiltered]

  // 4. Sort in memory
  combined.sort((a, b) => {
    // VIP goes first always
    if (a.isVip && !b.isVip) return -1
    if (!a.isVip && b.isVip) return 1

    // New goes next
    if (a.isNew && !b.isNew) return -1
    if (!a.isNew && b.isNew) return 1

    // Apply sort choice
    if (sort === 'price_asc') {
      return a.price - b.price
    }
    if (sort === 'price_desc') {
      return b.price - a.price
    }
    if (sort === 'area_asc') {
      return a.area - b.area
    }
    if (sort === 'area_desc') {
      return b.area - a.area
    }
    
    // Default 'latest': order by createdAt
    const dateA = a.createdAt ? new Date(a.createdAt).getTime() : 0
    const dateB = b.createdAt ? new Date(b.createdAt).getTime() : 0
    return dateB - dateA
  })

  // 5. Paginate in memory
  const totalItems = combined.length
  const totalPages = Math.ceil(totalItems / limit)
  
  const startIndex = (page - 1) * limit
  const endIndex = page * limit
  const paginatedList = combined.slice(startIndex, endIndex)

  return (
    <div className="bg-slate-50 pt-28 pb-16 min-h-screen">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {/* Breadcrumbs */}
        <nav className="flex text-xs font-semibold text-slate-500 mb-6 space-x-2 text-left" aria-label="Breadcrumb">
          <Link href="/" className="hover:text-primary transition">Trang chủ</Link>
          <span>/</span>
          {purpose === 'rent' ? (
            <>
              <Link href="/listings" className="hover:text-primary transition">Nhà đất</Link>
              <span>/</span>
              <span className="text-slate-800 font-bold">Cho thuê</span>
            </>
          ) : purpose === 'sale' ? (
            <>
              <Link href="/listings" className="hover:text-primary transition">Nhà đất</Link>
              <span>/</span>
              <span className="text-slate-800 font-bold">Mua bán</span>
            </>
          ) : (
            <span className="text-slate-800 font-bold">Nhà đất</span>
          )}
        </nav>

        {/* Page Header */}
        <div className="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 text-left">
          <div>
            <h1 className="text-2xl sm:text-3xl font-extrabold text-slate-900">
              {purpose === 'rent' ? 'Nhà đất cho thuê' : purpose === 'sale' ? 'Nhà đất mua bán' : 'Danh sách nhà đất'}
            </h1>
            <p className="text-xs text-slate-500 mt-1">
              Tìm thấy <span className="font-bold text-primary">{totalItems}</span> tin đăng phù hợp trên toàn quốc
            </p>
          </div>
        </div>

        {/* Search & Filter Panel */}
        <FilterPanel />

        {/* Listings Content */}
        <div className="w-full">
          <main className="w-full">
            
            {/* Sorting Header */}
            <div className="bg-white rounded-2xl px-5 py-3.5 border border-slate-100/80 shadow-sm flex items-center justify-between mb-8">
              <span className="text-xs text-slate-400 font-bold hidden sm:inline">Xem dạng lưới</span>
              <SortSelector currentSort={sort} />
            </div>

            {/* Property Cards Grid */}
            {paginatedList.length > 0 ? (
              <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 mb-12">
                {paginatedList.map(property => (
                  <PropertyCard key={property.id} property={property} />
                ))}
              </div>
            ) : (
              <div className="py-16 text-center bg-white rounded-3xl border border-slate-100 p-8 shadow-sm">
                <div className="w-20 h-20 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4">
                  <i className="fa-solid fa-folder-open text-2xl text-slate-400"></i>
                </div>
                <h3 className="text-slate-800 font-bold mb-1">Không tìm thấy kết quả</h3>
                <p className="text-xs text-slate-400 max-w-sm mx-auto">Vui lòng thay đổi từ khóa hoặc bộ lọc để tìm thấy bất động sản mong muốn.</p>
              </div>
            )}

            {/* Pagination Controls */}
            <Pagination currentPage={page} totalPages={totalPages} />
          </main>
        </div>
      </div>
    </div>
  )
}
