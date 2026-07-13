import { prisma } from '@/lib/prisma'
import { notFound } from 'next/navigation'
import Link from 'next/link'
import PropertyCard from '@/components/property/PropertyCard'

export const dynamic = 'force-dynamic'

interface ProjectDetailPageProps {
  params: Promise<{ slug: string }>
}

export default async function ProjectDetailPage({ params }: ProjectDetailPageProps) {
  const resolvedParams = await params
  const { slug } = resolvedParams

  const project = await prisma.project.findUnique({
    where: { slug }
  })

  if (!project) {
    notFound()
  }

  // Parse images array
  let imagesArr: string[] = []
  if (project.images) {
    try {
      if (typeof project.images === 'string') {
        imagesArr = JSON.parse(project.images)
      } else if (Array.isArray(project.images)) {
        imagesArr = project.images as string[]
      }
    } catch (err) {
      console.warn('Failed to parse project images:', err)
    }
  }

  // Fetch properties belonging to this project
  const dbProperties = await prisma.property.findMany({
    where: {
      projectId: Number(project.id),
      status: 'approved',
      deletedAt: null
    },
    include: {
      propertyImages: {
        where: { isPrimary: true }
      }
    },
    orderBy: { createdAt: 'desc' }
  })

  const mappedProperties = dbProperties.map(p => ({
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

  return (
    <div className="bg-slate-50 min-h-screen pt-28 pb-16 text-slate-800 text-left">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        {/* Breadcrumbs */}
        <nav className="flex text-xs font-semibold text-slate-500 mb-6 space-x-2">
          <Link href="/" className="hover:text-primary transition">Trang chủ</Link>
          <span>/</span>
          <Link href="/projects" className="hover:text-primary transition">Dự án bất động sản</Link>
          <span>/</span>
          <span className="text-slate-850 truncate max-w-xs">{project.title}</span>
        </nav>

        {/* Project Card Info Container */}
        <div className="bg-white rounded-3xl border border-slate-100 shadow-xl overflow-hidden p-6 sm:p-10 space-y-8">
          
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {/* Gallery / Image Slider */}
            <div className="space-y-3">
              <div className="w-full h-64 sm:h-[350px] rounded-2xl overflow-hidden bg-slate-100">
                <img src={imagesArr[0] || 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=600&q=80'} className="w-full h-full object-cover" alt={project.title} />
              </div>
              
              {/* Secondary Thumbnails */}
              {imagesArr.length > 1 && (
                <div className="grid grid-cols-4 gap-2.5">
                  {imagesArr.slice(1, 5).map((img, i) => (
                    <div key={i} className="h-16 rounded-xl overflow-hidden bg-slate-100 border border-slate-100">
                      <img src={img} className="w-full h-full object-cover" />
                    </div>
                  ))}
                </div>
              )}
            </div>

            {/* Spec Details */}
            <div className="space-y-5">
              <div>
                <span className={`inline-block px-2.5 py-0.5 rounded-md text-[9px] font-black uppercase ${
                  project.status === 'selling'
                    ? 'bg-emerald-50 text-emerald-650'
                    : project.status === 'upcoming'
                    ? 'bg-amber-50 text-amber-605'
                    : 'bg-slate-100 text-slate-550'
                }`}>
                  {project.status === 'selling' ? 'Đang mở bán' : project.status === 'upcoming' ? 'Sắp mở bán' : 'Đã bàn giao'}
                </span>
                <h1 className="text-xl sm:text-2xl font-black text-slate-900 mt-2 leading-tight">{project.title}</h1>
                <p className="text-xs text-slate-400 font-semibold mt-1"><i className="fa-solid fa-location-dot mr-1" />{project.location}, {project.district}, {project.city}</p>
              </div>

              <div className="border-t border-b border-slate-100 py-4 grid grid-cols-2 gap-4 text-xs font-semibold">
                <div className="space-y-1">
                  <span className="block text-[8px] uppercase tracking-wider text-slate-400">Chủ đầu tư</span>
                  <span className="text-slate-800 font-bold text-xs">{project.investor || 'Chưa cập nhật'}</span>
                </div>
                <div className="space-y-1">
                  <span className="block text-[8px] uppercase tracking-wider text-slate-400">Quy mô</span>
                  <span className="text-slate-800 font-bold text-xs">{project.scale || 'Chưa cập nhật'}</span>
                </div>
                <div className="space-y-1">
                  <span className="block text-[8px] uppercase tracking-wider text-slate-400">Khoảng giá dự án</span>
                  <span className="text-primary font-black text-xs">{project.priceRange || 'Thương lượng'}</span>
                </div>
              </div>

              <div className="space-y-2">
                <span className="block text-[9px] uppercase tracking-wider text-slate-400 font-bold">Giới thiệu dự án</span>
                <p className="text-xs text-slate-500 font-medium leading-relaxed whitespace-pre-wrap">{project.description}</p>
              </div>
            </div>
          </div>

        </div>

        {/* Properties in this project */}
        <section className="space-y-6">
          <div className="border-b border-slate-200 pb-3">
            <h2 className="text-lg font-black text-slate-850">Tin rao thuê thuộc dự án này ({mappedProperties.length})</h2>
          </div>

          {mappedProperties.length > 0 ? (
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
              {mappedProperties.map(property => (
                <PropertyCard key={property.id} property={property} />
              ))}
            </div>
          ) : (
            <div className="text-center py-16 bg-white border border-slate-100 rounded-3xl p-8 shadow-sm">
              <i className="fa-solid fa-folder-open text-slate-300 text-4xl mb-4 block" />
              <p className="text-slate-400 text-xs font-bold">Chưa có tin rao cho thuê nào thuộc dự án này.</p>
            </div>
          )}
        </section>

      </div>
    </div>
  )
}
