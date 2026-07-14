import { prisma } from '@/lib/prisma'
import { notFound } from 'next/navigation'
import Link from 'next/link'
import PropertyCard from '@/components/property/PropertyCard'
import ProjectGallery from '@/components/project/ProjectGallery'
import DetailMapWrapper from '@/components/property/DetailMapWrapper'

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

  // Fetch properties belonging to this project (both sale and rent)
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
    <div className="bg-slate-50 min-h-screen pt-24 pb-16 text-slate-800 text-left">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        
        {/* Breadcrumbs */}
        <nav className="flex text-sm font-semibold text-slate-500 mb-6 space-x-2">
          <Link href="/" className="hover:text-primary transition">Trang chủ</Link>
          <span>/</span>
          <Link href="/projects" className="hover:text-primary transition">Dự án</Link>
          <span>/</span>
          <span className="text-slate-800 font-bold truncate max-w-xs">{project.title}</span>
        </nav>

        {/* Project Title Header */}
        <div className="bg-white rounded-3xl p-6 md:p-8 border border-slate-100 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4 text-left">
          <div>
            <div className="flex items-center gap-2 mb-2">
              <span className="bg-primary/10 text-primary text-xs font-black px-3 py-1 rounded-full uppercase tracking-wider">{project.investor || 'Chủ đầu tư'}</span>
              <span className={`text-xs font-bold px-3 py-1 rounded-full ${
                project.status === 'selling'
                  ? 'bg-emerald-100 text-emerald-700'
                  : project.status === 'upcoming'
                  ? 'bg-orange-100 text-orange-700'
                  : 'bg-blue-100 text-blue-700'
              }`}>
                {project.status === 'selling' ? 'Đang mở bán' : project.status === 'upcoming' ? 'Sắp mở bán' : 'Đã bàn giao'}
              </span>
            </div>
            <h1 className="text-2xl md:text-3xl font-extrabold text-slate-900">{project.title}</h1>
            <p className="text-sm text-slate-500 mt-1 flex items-center">
              <i className="fa-solid fa-location-dot mr-1.5 text-primary"></i> {project.location}
            </p>
          </div>
          
          <div className="flex flex-col text-right">
            <span className="text-slate-400 text-xs font-bold uppercase tracking-wider">Giá bán dự kiến</span>
            <span className="text-2xl md:text-3xl font-black text-orange-500 mt-0.5">{project.priceRange || 'Liên hệ'}</span>
          </div>
        </div>

        {/* Layout Grid */}
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
          {/* Left 2 Cols: Gallery, Description & Map */}
          <div className="lg:col-span-2 space-y-8 text-left">
            {/* Gallery Component */}
            <ProjectGallery images={imagesArr} title={project.title} />

            {/* Description & Overview */}
            <div className="bg-white rounded-3xl p-6 md:p-8 border border-slate-100 shadow-sm">
              <h2 className="text-xl font-extrabold text-slate-900 mb-4 pb-3 border-b border-slate-50">Mô tả dự án</h2>
              <div className="prose max-w-none text-slate-650 leading-relaxed text-sm md:text-base space-y-4">
                <p className="whitespace-pre-line leading-relaxed">{project.description}</p>
              </div>
            </div>

            {/* Map Coordinates if available */}
            {project.latitude && project.longitude && (
              <div className="bg-white rounded-3xl p-6 md:p-8 border border-slate-100 shadow-sm">
                <h2 className="text-xl font-extrabold text-slate-900 mb-4">Vị trí dự án</h2>
                <DetailMapWrapper
                  latitude={Number(project.latitude)}
                  longitude={Number(project.longitude)}
                  title={project.title}
                />
              </div>
            )}
          </div>

          {/* Right 1 Col: Quick Facts & Contact CTA */}
          <div className="space-y-8 text-left">
            {/* Project Details Panel */}
            <div className="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm">
              <h3 className="text-lg font-extrabold text-slate-900 mb-4 pb-3 border-b border-slate-50">Thông tin tổng quan</h3>
              
              <dl className="space-y-4 text-sm">
                <div className="flex justify-between py-1 border-b border-slate-50 pb-2">
                  <dt className="text-slate-400 font-semibold">Chủ đầu tư:</dt>
                  <dd className="text-slate-800 font-extrabold text-right">{project.investor || 'Chủ đầu tư'}</dd>
                </div>
                <div className="flex justify-between py-1 border-b border-slate-50 pb-2">
                  <dt className="text-slate-400 font-semibold">Quy mô:</dt>
                  <dd className="text-slate-800 font-extrabold text-right">{project.scale || 'Đang cập nhật'}</dd>
                </div>
                <div className="flex justify-between py-1 border-b border-slate-50 pb-2">
                  <dt className="text-slate-400 font-semibold">Trạng thái:</dt>
                  <dd className="text-slate-800 font-extrabold text-right">
                    {project.status === 'selling' ? 'Đang mở bán' : project.status === 'upcoming' ? 'Sắp mở bán' : 'Đã bàn giao'}
                  </dd>
                </div>
                <div className="flex justify-between py-1">
                  <dt className="text-slate-400 font-semibold">Địa chỉ:</dt>
                  <dd className="text-slate-800 font-extrabold text-right max-w-[180px] truncate" title={project.location || undefined}>{project.location}</dd>
                </div>
              </dl>
            </div>

            {/* Call to action card */}
            <div className="bg-gradient-to-br from-primary to-primary-hover rounded-3xl p-6 text-white shadow-xl shadow-primary/20">
              <h3 className="text-lg font-extrabold mb-2">Quan tâm dự án này?</h3>
              <p className="text-xs text-white/80 leading-relaxed mb-6">
                Để lại thông tin liên hệ hoặc gọi điện cho chúng tôi để nhận bảng giá chính thức, tài liệu mặt bằng và chính sách bán hàng mới nhất của dự án.
              </p>
              <a 
                href="tel:19001888" 
                className="w-full inline-flex items-center justify-center py-3.5 px-4 rounded-2xl bg-white text-primary font-black hover:bg-slate-50 transition text-sm shadow-md cursor-pointer"
              >
                <i className="fa-solid fa-phone mr-2"></i> Gọi ngay: 1900 1888
              </a>
            </div>
          </div>
        </div>

        {/* Project Properties section */}
        <section className="space-y-6 text-left mt-12">
          <div className="border-b border-slate-200 pb-3">
            <h2 className="text-2xl font-black text-slate-900">Bất động sản thuộc dự án này</h2>
            <p className="text-sm text-slate-500 mt-1">Danh sách tin đăng mua bán, cho thuê thực tế đang hoạt động tại dự án {project.title}</p>
          </div>

          {mappedProperties.length > 0 ? (
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-8">
              {mappedProperties.map(property => (
                <PropertyCard key={property.id} property={property} />
              ))}
            </div>
          ) : (
            <div className="text-center py-16 bg-white border border-slate-100 rounded-3xl p-8 shadow-sm">
              <div className="w-16 h-16 rounded-full bg-slate-50 flex items-center justify-center mx-auto mb-4 text-slate-400">
                <i className="fa-solid fa-house-circle-xmark text-2xl" />
              </div>
              <h3 className="text-lg font-bold text-slate-800 mb-1">Chưa có tin đăng liên quan</h3>
              <p className="text-slate-500 text-sm">Hiện chưa có chủ nhà hoặc nhà môi giới nào đăng tin mua bán/cho thuê thuộc dự án này.</p>
            </div>
          )}
        </section>

      </div>
    </div>
  )
}
