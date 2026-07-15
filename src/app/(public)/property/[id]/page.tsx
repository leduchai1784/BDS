import { prisma } from '@/lib/prisma'
import { getNksProperties } from '@/lib/nks'
import ImageGallery from '@/components/property/ImageGallery'
import BookingForm from '@/components/appointment/BookingForm'
import PropertyCard from '@/components/property/PropertyCard'
import Link from 'next/link'
import DetailMapWrapper from '@/components/property/DetailMapWrapper'
import { notFound } from 'next/navigation'
import { auth } from '@/lib/auth'
import PropertyActions from '@/components/property/PropertyActions'

export const dynamic = 'force-dynamic'

interface PropertyDetailPageProps {
  params: Promise<{ id: string }>
}

export default async function PropertyDetailPage({ params }: PropertyDetailPageProps) {
  const resolvedParams = await params
  const id = resolvedParams.id

  const session = await auth()
  const userId = session?.user?.id ? Number(session.user.id) : null

  let isFavorite = false
  if (userId) {
    const fav = await prisma.wishlist.findUnique({
      where: {
        userId_propertyId: {
          userId,
          propertyId: id
        }
      }
    })
    isFavorite = !!fav
  }

  const isUuid = /^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i.test(id)

  let property: any = null
  let isDbProperty = false

  if (isUuid) {
    // 1. Fetch from Database
    const dbProp = await prisma.property.findUnique({
      where: { id },
      include: {
        propertyImages: true,
        owner: true,
        category: true
      }
    })

    if (dbProp && !dbProp.deletedAt) {
      isDbProperty = true

      // Increment views count in database
      await prisma.property.update({
        where: { id },
        data: { viewsCount: { increment: 1 } }
      }).catch(err => console.error('Failed to increment viewsCount:', err))

      // Format property details
      const images = dbProp.propertyImages.map(img => img.imagePath)
      const agentName = dbProp.owner.name || 'Chủ nhà'
      const agentPhone = dbProp.phone || dbProp.owner.phone || '0977.758.217'
      const agentAvatar = dbProp.owner.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(agentName)}&background=0077bb&color=fff`

      property = {
        id: dbProp.id,
        title: dbProp.title,
        description: dbProp.description,
        price: Number(dbProp.price),
        priceLabel: dbProp.priceLabel,
        deposit: dbProp.deposit ? Number(dbProp.deposit) : null,
        leaseTerm: dbProp.leaseTerm,
        area: dbProp.area,
        bedroom: dbProp.bedroom,
        bathroom: dbProp.bathroom,
        direction: dbProp.direction || 'Không xác định',
        furniture: dbProp.furniture || 'Cơ bản',
        legal: dbProp.legal || 'Đầy đủ',
        floors: dbProp.floors,
        frontage: dbProp.frontage,
        roadWidth: dbProp.roadWidth,
        address: dbProp.address,
        ward: dbProp.ward,
        district: dbProp.district,
        city: dbProp.city,
        latitude: dbProp.latitude,
        longitude: dbProp.longitude,
        isVip: dbProp.isVip,
        isNew: dbProp.isNew,
        propertyType: dbProp.propertyType,
        categoryId: dbProp.categoryId ? Number(dbProp.categoryId) : null,
        ownerId: dbProp.ownerId ? Number(dbProp.ownerId) : null,
        images,
        agent: {
          name: agentName,
          phone: agentPhone,
          avatar: agentAvatar,
          email: dbProp.owner.email,
          zalo: dbProp.zalo || dbProp.owner.phone
        }
      }
    }
  }

  // 2. Fetch from NKS if not found in local DB
  if (!property) {
    const nksList = await getNksProperties()
    const nksProp = nksList.find(p => p.id === id)

    if (nksProp) {
      // Map NKS details
      const agentName = 'Môi giới BDS'
      const agentPhone = '0977.758.217'
      const agentAvatar = `https://ui-avatars.com/api/?name=${encodeURIComponent(agentName)}&background=0077bb&color=fff`

      property = {
        id: nksProp.id,
        title: nksProp.title,
        description: nksProp.description || nksProp.title,
        price: nksProp.price,
        priceLabel: nksProp.priceLabel,
        deposit: null,
        leaseTerm: 'Thỏa thuận',
        area: nksProp.area,
        bedroom: nksProp.bedroom,
        bathroom: nksProp.bathroom,
        direction: 'Không xác định',
        furniture: 'Cơ bản',
        legal: 'Sổ đỏ/Sổ hồng',
        floors: nksProp.floors,
        frontage: null,
        roadWidth: null,
        address: nksProp.address,
        ward: '',
        district: nksProp.district,
        city: nksProp.city,
        latitude: nksProp.latitude,
        longitude: nksProp.longitude,
        isVip: nksProp.isVip,
        isNew: nksProp.isNew,
        propertyType: nksProp.propertyType,
        images: [nksProp.imagePath],
        agent: {
          name: agentName,
          phone: agentPhone,
          avatar: agentAvatar,
          email: 'info@bdsrental.vn',
          zalo: agentPhone
        }
      }
    }
  }

  if (!property) {
    notFound()
  }

  // 3. Fetch similar properties (excluding current)
  let similarProperties: any[] = []
  if (isDbProperty) {
    const similarDb = await prisma.property.findMany({
      where: {
        status: 'approved',
        deletedAt: null,
        id: { not: property.id },
        propertyType: property.propertyType
      },
      take: 4,
      include: {
        propertyImages: {
          where: { isPrimary: true }
        }
      }
    })
    similarProperties = similarDb.map(p => ({
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
      imagePath: p.propertyImages?.[0]?.imagePath || null
    }))
  } else {
    // Similar NKS properties
    const nksList = await getNksProperties()
    similarProperties = nksList
      .filter(p => p.id !== property.id && p.propertyType === property.propertyType)
      .slice(0, 4)
  }

  const isSale = property.priceLabel.toLowerCase().indexOf('tháng') === -1 && 
                 property.priceLabel.toLowerCase().indexOf('thang') === -1

  return (
    <div className="bg-slate-50 pt-28 pb-16 min-h-screen">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {/* Breadcrumbs Section */}
        <nav className="flex text-xs font-semibold text-slate-500 mb-6 space-x-2 text-left" aria-label="Breadcrumb">
          <Link href="/" className="hover:text-primary transition">Trang chủ</Link>
          <span>/</span>
          <Link href="/listings" className="hover:text-primary transition">Nhà đất</Link>
          <span>/</span>
          <span className="text-slate-800 truncate max-w-[200px] sm:max-w-none">{property.title}</span>
        </nav>

        {/* Details Workspace Grid */}
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
          
          {/* Left Column: Gallery & Info */}
          <div className="lg:col-span-8 space-y-10">
            {/* Image Gallery */}
            <ImageGallery images={property.images} isVip={property.isVip} />

            {/* Info details card */}
            <div className="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm text-left space-y-8">
              
              {/* Title & Price Header */}
              <div className="border-b border-slate-100 pb-6">
                <div className="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                  <div className="space-y-3">
                    <h1 className="text-xl sm:text-2xl font-extrabold text-slate-900 leading-snug">
                      {isSale ? (
                        <span className="inline-flex items-center px-2.5 py-0.5 rounded-lg text-[10px] font-black bg-orange-500 text-white mr-2 align-middle">
                          <i className="fa-solid fa-tags mr-1"></i> BÁN
                        </span>
                      ) : (
                        <span className="inline-flex items-center px-2.5 py-0.5 rounded-lg text-[10px] font-black bg-cyan-600 text-white mr-2 align-middle">
                          <i className="fa-solid fa-key mr-1"></i> THUÊ
                        </span>
                      )}
                      {property.title}
                    </h1>
                    <div className="flex items-center text-slate-400 text-xs font-medium">
                      <i className="fa-solid fa-location-dot text-slate-400 mr-2 text-sm flex-shrink-0"></i>
                      <span>{property.address}</span>
                    </div>
                  </div>
                  
                  {/* Price info block */}
                  <div className="flex sm:flex-col items-baseline sm:items-end justify-between sm:justify-start gap-2 flex-shrink-0">
                    <div className="text-xl sm:text-2xl font-black text-cyan-650">{property.priceLabel}</div>
                    <div className="text-xs font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-lg">
                      {property.area} m²
                    </div>
                  </div>
                </div>
              </div>

              {/* Specifications details */}
              <div>
                <h3 className="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
                  <i className="fa-solid fa-circle-info text-primary"></i>
                  <span>Thông số kỹ thuật</span>
                </h3>
                
                <div className="grid grid-cols-2 sm:grid-cols-3 gap-6">
                  {/* Area */}
                  <div className="flex items-start space-x-3.5">
                    <div className="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-cyan-650 flex-shrink-0">
                      <i className="fa-solid fa-ruler-combined text-base"></i>
                    </div>
                    <div>
                      <span className="text-xs text-slate-400 font-semibold block mb-0.5">Diện tích</span>
                      <span className="text-sm font-extrabold text-slate-800">{property.area} m²</span>
                    </div>
                  </div>

                  {/* Bedrooms */}
                  {property.bedroom > 0 && (
                    <div className="flex items-start space-x-3.5">
                      <div className="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-cyan-650 flex-shrink-0">
                        <i className="fa-solid fa-bed text-base"></i>
                      </div>
                      <div>
                        <span className="text-xs text-slate-400 font-semibold block mb-0.5">Phòng ngủ</span>
                        <span className="text-sm font-extrabold text-slate-800">{property.bedroom} PN</span>
                      </div>
                    </div>
                  )}

                  {/* Bathrooms */}
                  {property.bathroom > 0 && (
                    <div className="flex items-start space-x-3.5">
                      <div className="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-cyan-650 flex-shrink-0">
                        <i className="fa-solid fa-bath text-base"></i>
                      </div>
                      <div>
                        <span className="text-xs text-slate-400 font-semibold block mb-0.5">Phòng tắm</span>
                        <span className="text-sm font-extrabold text-slate-800">{property.bathroom} WC</span>
                      </div>
                    </div>
                  )}

                  {/* Direction */}
                  <div className="flex items-start space-x-3.5">
                    <div className="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-cyan-650 flex-shrink-0">
                      <i className="fa-solid fa-compass text-base"></i>
                    </div>
                    <div>
                      <span className="text-xs text-slate-400 font-semibold block mb-0.5">Hướng</span>
                      <span className="text-sm font-extrabold text-slate-800">{property.direction}</span>
                    </div>
                  </div>

                  {/* Furniture */}
                  <div className="flex items-start space-x-3.5">
                    <div className="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-cyan-650 flex-shrink-0">
                      <i className="fa-solid fa-chair text-base"></i>
                    </div>
                    <div>
                      <span className="text-xs text-slate-400 font-semibold block mb-0.5">Nội thất</span>
                      <span className="text-sm font-extrabold text-slate-800 truncate block max-w-[150px]" title={property.furniture}>
                        {property.furniture}
                      </span>
                    </div>
                  </div>

                  {/* Legal */}
                  <div className="flex items-start space-x-3.5">
                    <div className="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-cyan-650 flex-shrink-0">
                      <i className="fa-solid fa-file-contract text-base"></i>
                    </div>
                    <div>
                      <span className="text-xs text-slate-400 font-semibold block mb-0.5">Pháp lý</span>
                      <span className="text-sm font-extrabold text-slate-800 truncate block max-w-[150px]" title={property.legal}>
                        {property.legal}
                      </span>
                    </div>
                  </div>

                  {/* Floors */}
                  {property.floors > 0 && (
                    <div className="flex items-start space-x-3.5">
                      <div className="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-cyan-650 flex-shrink-0">
                        <i className="fa-solid fa-layer-group text-base"></i>
                      </div>
                      <div>
                        <span className="text-xs text-slate-400 font-semibold block mb-0.5">Số tầng</span>
                        <span className="text-sm font-extrabold text-slate-800">{property.floors} tầng</span>
                      </div>
                    </div>
                  )}

                  {/* Frontage */}
                  {property.frontage > 0 && (
                    <div className="flex items-start space-x-3.5">
                      <div className="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-cyan-650 flex-shrink-0">
                        <i className="fa-solid fa-arrows-left-right text-base"></i>
                      </div>
                      <div>
                        <span className="text-xs text-slate-400 font-semibold block mb-0.5">Mặt tiền</span>
                        <span className="text-sm font-extrabold text-slate-800">{property.frontage} m</span>
                      </div>
                    </div>
                  )}

                  {/* Deposit */}
                  {property.deposit > 0 && (
                    <div className="flex items-start space-x-3.5">
                      <div className="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-cyan-650 flex-shrink-0">
                        <i className="fa-solid fa-hand-holding-dollar text-base"></i>
                      </div>
                      <div>
                        <span className="text-xs text-slate-400 font-semibold block mb-0.5">Đặt cọc</span>
                        <span className="text-sm font-extrabold text-slate-800">
                          {property.deposit.toLocaleString('vi-VN')} đ
                        </span>
                      </div>
                    </div>
                  )}
                </div>
              </div>

              {/* Description */}
              <div className="border-t border-slate-100 pt-8">
                <h3 className="text-lg font-bold text-slate-800 mb-5 flex items-center gap-2">
                  <i className="fa-solid fa-align-left text-primary"></i>
                  <span>Mô tả chi tiết</span>
                </h3>
                <div 
                  className="text-slate-600 text-sm leading-relaxed space-y-4 font-medium whitespace-pre-line"
                >
                  {property.description}
                </div>
              </div>

              {/* Map Canvas */}
              <div className="border-t border-slate-100 pt-8">
                <h3 className="text-lg font-bold text-slate-800 mb-5 flex items-center gap-2">
                  <i className="fa-solid fa-map-location-dot text-primary"></i>
                  <span>Bản đồ vị trí</span>
                </h3>
                <DetailMapWrapper 
                  latitude={property.latitude} 
                  longitude={property.longitude} 
                  title={property.title} 
                />
                
                <div className="mt-4">
                  <Link 
                    href={`/map?lat=${property.latitude}&lng=${property.longitude}&id=${property.id}`}
                    className="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-[11px] font-extrabold bg-slate-100 hover:bg-primary hover:text-white border border-slate-200/60 shadow-xs transition duration-200"
                  >
                    <i className="fa-solid fa-expand text-xs"></i>
                    <span>Xem bản đồ lớn</span>
                  </Link>
                </div>
              </div>
            </div>
          </div>

          {/* Right Column: Owner & Booking Scheduler Form */}
          <div id="booking-section" className="lg:col-span-4 lg:sticky lg:top-24 space-y-6">
            <div className="bg-white rounded-3xl p-6 border border-slate-100 shadow-md text-left relative">
              
              {/* Agent card details */}
              <div className="flex items-center space-x-4 pb-4 border-b border-slate-100 mb-5">
                <img 
                  src={property.agent.avatar} 
                  alt={property.agent.name} 
                  className="w-14 h-14 rounded-full object-cover border border-slate-150 shadow-sm"
                />
                <div>
                  <h4 className="text-base font-bold text-slate-800 leading-tight mb-0.5">{property.agent.name}</h4>
                  <span className="text-xs font-semibold text-slate-400 block">Chủ nhà chính chủ</span>
                </div>
              </div>

              {/* Call and Zalo contact anchors */}
              <div className="grid grid-cols-2 gap-3 mb-3">
                <a 
                  href={`tel:${property.agent.phone}`}
                  className="inline-flex items-center justify-center px-2 py-3 rounded-2xl text-white bg-green-500 hover:bg-green-600 shadow-md shadow-green-500/25 hover:shadow-green-600/35 transition font-bold text-xs cursor-pointer truncate"
                >
                  <i className="fa-solid fa-phone mr-1.5"></i> Gọi ngay
                </a>
                
                <a 
                  href={`https://zalo.me/${property.agent.zalo.replace(/[^0-9]/g, '')}`}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="inline-flex items-center justify-center px-2 py-3 rounded-2xl text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-500/25 hover:shadow-blue-600/35 transition font-bold text-xs cursor-pointer truncate"
                >
                  Chat Zalo
                </a>
              </div>

              {/* Share & Favorite listing actions */}
              <PropertyActions 
                propertyId={property.id} 
                propertyTitle={property.title} 
                isFavoriteInitial={isFavorite}
              />

              {/* Booking Scheduler Form */}
              <BookingForm 
                propertyId={property.id} 
                propertyTitle={property.title}
                propertyOwnerId={property.ownerId || 0}
                agentName={property.agent.name}
              />
            </div>
          </div>
        </div>

        {/* Similar Listings Section */}
        <section className="mt-20 pt-12 border-t border-slate-200/60 text-left">
          <div className="mb-10">
            <span className="text-xs font-bold text-primary tracking-widest uppercase mb-1.5 block">Khám phá thêm</span>
            <h2 className="text-2xl sm:text-3xl font-extrabold text-slate-900 leading-tight">Bất Động Sản Tương Tự</h2>
          </div>

          {similarProperties.length > 0 ? (
            <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
              {similarProperties.map(simProperty => (
                <PropertyCard key={simProperty.id} property={simProperty} />
              ))}
            </div>
          ) : (
            <div className="text-center py-8 bg-white border border-slate-100 rounded-2xl text-slate-400 font-semibold">
              Chưa có bất động sản tương tự nào.
            </div>
          )}
        </section>

      </div>
    </div>
  )
}
