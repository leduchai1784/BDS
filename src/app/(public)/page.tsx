import { prisma } from '@/lib/prisma'
import Hero from '@/components/hero/Hero'
import PropertyCard from '@/components/property/PropertyCard'
import ProjectSlider from '@/components/home/ProjectSlider'
import DemandSlider from '@/components/home/DemandSlider'
import VideoShowcase from '@/components/home/VideoShowcase'
import NewsTabs from '@/components/home/NewsTabs'
import Link from 'next/link'

export const dynamic = 'force-dynamic'

export default async function HomePage() {
  // 1. Fetch featured properties (approved, not deleted, order by VIP then New/Created)
  const featuredDb = await prisma.property.findMany({
    where: {
      status: 'approved',
      deletedAt: null
    },
    take: 8,
    orderBy: [
      { isVip: 'desc' },
      { isNew: 'desc' },
      { createdAt: 'desc' }
    ],
    include: {
      propertyImages: {
        where: { isPrimary: true }
      }
    }
  })

  // 2. Fetch latest properties (approved, not deleted, order by created_at desc)
  const latestDb = await prisma.property.findMany({
    where: {
      status: 'approved',
      deletedAt: null
    },
    take: 4,
    orderBy: {
      createdAt: 'desc'
    },
    include: {
      propertyImages: {
        where: { isPrimary: true }
      }
    }
  })

  // 3. Fetch featured projects
  const projects = await prisma.project.findMany({
    take: 6,
    orderBy: {
      createdAt: 'desc'
    }
  })

  // Map database structures to fit PropertyCard components
  const mapPropertyForCard = (p: any) => ({
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
  })

  const featuredList = featuredDb.map(mapPropertyForCard)
  const latestList = latestDb.map(mapPropertyForCard)

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
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 mb-12">
            {featuredList.map(property => (
              <PropertyCard key={property.id} property={property} />
            ))}
          </div>
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
    </div>
  )
}
