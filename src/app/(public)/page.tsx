import { prisma } from '@/lib/prisma'
import { getNksProperties } from '@/lib/nks'
import Hero from '@/components/hero/Hero'
import PropertyCard from '@/components/property/PropertyCard'
import ProjectSlider from '@/components/home/ProjectSlider'
import DemandSlider from '@/components/home/DemandSlider'
import VideoShowcase from '@/components/home/VideoShowcase'
import NewsTabs from '@/components/home/NewsTabs'
import Link from 'next/link'

export const dynamic = 'force-dynamic'

export default async function HomePage() {
  // 1. Fetch Database properties (approved, not deleted)
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

  // Map database structures to fit PropertyCard components
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

  // 2. Fetch NKS API properties
  const nksList = await getNksProperties()

  // 3. Combine both lists
  const combined = [...dbList, ...nksList]

  // 4. Filter and slice
  // Tin đăng nổi bật (Featured Listings): only isVip === true, sorted by latest date, take 8
  const featuredList = combined
    .filter(p => p.isVip)
    .sort((a, b) => {
      const dateA = a.createdAt ? new Date(a.createdAt).getTime() : 0
      const dateB = b.createdAt ? new Date(b.createdAt).getTime() : 0
      return dateB - dateA
    })
    .slice(0, 8)

  // Tin đăng mới nhất (Latest Listings): sorted by latest date, take 4
  const latestList = combined
    .slice()
    .sort((a, b) => {
      const dateA = a.createdAt ? new Date(a.createdAt).getTime() : 0
      const dateB = b.createdAt ? new Date(b.createdAt).getTime() : 0
      return dateB - dateA
    })
    .slice(0, 4)

  // 5. Fetch featured projects
  const projects = await prisma.project.findMany({
    take: 6,
    orderBy: {
      createdAt: 'desc'
    }
  })

  // Map Decimal prices/lat/lng to numbers for Project components
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

  return (
    <div className="flex flex-col min-h-screen">
      {/* Hero Banner with Integrated search bar */}
      <Hero />

      {/* Section 2: Featured properties grid */}
      <section id="listings" className="py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 scroll-mt-24 text-left">
        <div className="mb-10">
          <span className="text-xs font-bold text-primary tracking-widest uppercase mb-2 block">Dành riêng cho bạn</span>
          <h2 className="text-3xl font-extrabold text-slate-900 leading-tight">Tin đăng nổi bật</h2>
        </div>

        {featuredList.length > 0 ? (
          <>
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 mb-12">
              {featuredList.map(property => (
                <PropertyCard key={property.id} property={property} />
              ))}
            </div>
            
            {/* Pagination Section (Static mockup to match PHP homepage) */}
            <div className="flex justify-center mt-12">
              <nav className="inline-flex space-x-1 bg-white p-2 rounded-2xl border border-slate-100 shadow-sm" aria-label="Pagination">
                <button type="button" className="inline-flex items-center justify-center w-10 h-10 rounded-xl text-slate-500 hover:bg-slate-50 hover:text-primary transition cursor-pointer">
                  <i className="fa-solid fa-chevron-left text-xs"></i>
                </button>
                <span className="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-primary text-white font-bold shadow-md shadow-primary/20 text-xs">1</span>
                <button type="button" className="inline-flex items-center justify-center w-10 h-10 rounded-xl text-slate-600 hover:bg-slate-50 hover:text-primary transition font-bold text-xs cursor-pointer">2</button>
                <button type="button" className="inline-flex items-center justify-center w-10 h-10 rounded-xl text-slate-600 hover:bg-slate-50 hover:text-primary transition font-bold text-xs cursor-pointer">3</button>
                <span className="inline-flex items-center justify-center w-10 h-10 text-slate-400 text-xs font-semibold">...</span>
                <button type="button" className="inline-flex items-center justify-center w-10 h-10 rounded-xl text-slate-600 hover:bg-slate-50 hover:text-primary transition font-bold text-xs cursor-pointer">12</button>
              </nav>
            </div>
          </>
        ) : (
          <div className="text-center py-12 bg-white rounded-3xl border border-slate-100/80 p-8 shadow-sm">
            <i className="fa-solid fa-folder-open text-slate-350 text-4xl mb-4 block"></i>
            <p className="text-slate-400 font-semibold">Chưa có tin đăng nổi bật nào khả dụng.</p>
          </div>
        )}
      </section>

      {/* Section 2.5: Latest properties grid */}
      <section className="py-16 bg-white border-t border-slate-100 text-left">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="mb-10 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
              <span className="text-xs font-bold text-primary tracking-widest uppercase mb-2 block">Cập nhật liên tục</span>
              <h2 className="text-3xl font-extrabold text-slate-900 leading-tight">Tin đăng mới nhất</h2>
            </div>
            <Link 
              href="/listings" 
              className="inline-flex items-center text-sm font-bold text-primary hover:text-primary-hover hover:underline transition"
            >
              Xem tất cả tin mới <i className="fa-solid fa-arrow-right ml-2 text-xs"></i>
            </Link>
          </div>

          {latestList.length > 0 ? (
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
              {latestList.map(property => (
                <PropertyCard key={property.id} property={property} />
              ))}
            </div>
          ) : (
            <div className="text-center py-12 bg-slate-50 rounded-3xl border border-slate-100/50 p-8 shadow-sm">
              <i className="fa-solid fa-folder-open text-slate-350 text-4xl mb-4 block"></i>
              <p className="text-slate-400 font-semibold">Chưa có tin đăng mới nào.</p>
            </div>
          )}
        </div>
      </section>

      {/* Section 2.7: Featured Projects Slider */}
      <section className="py-16 bg-white border-t border-slate-100 text-left">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <ProjectSlider projects={projectList} />
        </div>
      </section>

      {/* Section 3: Community demands slider */}
      <section className="py-16 bg-slate-50 border-t border-b border-slate-100 text-left">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <DemandSlider />
        </div>
      </section>

      {/* Section 4: Real estate video showcases */}
      <VideoShowcase />

      {/* Section 4.5: Tabbed Real Estate News */}
      <NewsTabs />

      {/* Section 5: Call to Action (CTA) */}
      <section className="pb-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-0 text-left">
        <div className="relative rounded-3xl bg-slate-900 overflow-hidden shadow-xl py-12 px-6 sm:px-12 md:py-16 md:px-16 text-left border border-slate-800">
          {/* Background effects */}
          <div className="absolute inset-0 opacity-10">
            <img 
              src="https://res.cloudinary.com/dj8t18pke/image/upload/v1782101764/ewjyvlwq88ixefrpstmu.jpg" 
              alt="Landlord CTA" 
              className="w-full h-full object-cover" 
            />
          </div>
          <div className="absolute -top-32 -right-32 w-80 h-80 rounded-full bg-primary/30 blur-3xl"></div>
          <div className="absolute -bottom-32 -left-32 w-80 h-80 rounded-full bg-primary/10 blur-3xl"></div>

          <div className="relative z-10 max-w-4xl flex flex-col justify-center h-full">
            <h2 className="text-3xl font-extrabold text-white sm:text-4xl leading-tight">
              Bạn có bất động sản <span className="text-primary">muốn cho thuê?</span>
            </h2>
            <p className="mt-4 text-base text-slate-300 leading-relaxed">
              Đăng tin ngay hôm nay để tiếp cận hơn 100,000 khách thuê tiềm năng truy cập mỗi tháng. Hoàn toàn miễn phí, nhanh chóng và dễ dàng.
            </p>
            <div className="mt-8 flex flex-col sm:flex-row gap-4">
              <Link 
                href="/property/create" 
                className="inline-flex items-center justify-center px-6 py-3.5 border border-transparent text-sm font-bold rounded-xl text-white bg-primary hover:bg-primary-hover shadow-lg shadow-primary/20 hover:shadow-primary/35 transition cursor-pointer"
              >
                <i className="fa-solid fa-circle-plus mr-2"></i> Đăng tin cho thuê ngay
              </Link>
              <Link 
                href="/#contact" 
                className="inline-flex items-center justify-center px-6 py-3.5 border border-slate-700 hover:border-slate-500 text-sm font-semibold rounded-xl text-slate-100 hover:text-white bg-slate-900/50 hover:bg-slate-900 transition cursor-pointer"
              >
                Liên hệ tư vấn môi giới
              </Link>
            </div>
          </div>
        </div>
      </section>
    </div>
  )
}
