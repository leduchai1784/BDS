import { prisma } from '@/lib/prisma'
import Link from 'next/link'
import Pagination from '@/components/property/Pagination'

export const dynamic = 'force-dynamic'

interface ProjectsPageProps {
  searchParams: Promise<Record<string, string | string[] | undefined>>
}

export default async function ProjectsPage({ searchParams }: ProjectsPageProps) {
  const resolvedParams = await searchParams
  
  const page = Number(resolvedParams.page || 1)
  const limit = 6
  const skip = (page - 1) * limit

  const q = typeof resolvedParams.q === 'string' ? resolvedParams.q.trim() : ''
  const city = typeof resolvedParams.city === 'string' ? resolvedParams.city.trim() : ''
  const status = typeof resolvedParams.status === 'string' ? resolvedParams.status.trim() : ''

  // Build where queries
  const where: any = {}
  
  if (q) {
    where.OR = [
      { title: { contains: q, mode: 'insensitive' } },
      { location: { contains: q, mode: 'insensitive' } },
      { investor: { contains: q, mode: 'insensitive' } }
    ]
  }

  if (city) {
    where.city = { contains: city, mode: 'insensitive' }
  }

  if (status && ['selling', 'upcoming', 'handed_over'].includes(status)) {
    where.status = status
  }

  const [dbProjects, totalCount] = await Promise.all([
    prisma.project.findMany({
      where,
      orderBy: { createdAt: 'desc' },
      skip,
      take: limit
    }),
    prisma.project.count({ where })
  ])

  const totalPages = Math.ceil(totalCount / limit)

  // Map BigInt / parse JSON
  const projectsList = dbProjects.map(p => {
    let imagesArr: string[] = []
    if (p.images) {
      try {
        if (typeof p.images === 'string') {
          imagesArr = JSON.parse(p.images)
        } else if (Array.isArray(p.images)) {
          imagesArr = p.images as string[]
        }
      } catch (err) {
        console.warn('Failed to parse project images:', err)
      }
    }
    return {
      id: Number(p.id),
      title: p.title,
      slug: p.slug,
      description: p.description,
      location: p.location,
      priceRange: p.priceRange,
      status: p.status,
      investor: p.investor,
      scale: p.scale,
      image: imagesArr[0] || 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=600&q=80'
    }
  })

  // Helper to build URL with search params
  const getFilterUrl = (statusVal?: string) => {
    const params = new URLSearchParams()
    if (q) params.set('q', q)
    if (city) params.set('city', city)
    if (statusVal) params.set('status', statusVal)
    return `/projects?${params.toString()}`
  }

  return (
    <div className="bg-slate-50 min-h-screen pt-28 pb-16 text-slate-800 text-left">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {/* Breadcrumbs */}
        <nav className="flex text-xs font-semibold text-slate-500 mb-6 space-x-2">
          <Link href="/" className="hover:text-primary transition">Trang chủ</Link>
          <span>/</span>
          <span className="text-slate-850">Dự án bất động sản</span>
        </nav>

        {/* Search Banner */}
        <div className="relative bg-slate-900 overflow-hidden py-16 px-6 sm:px-12 text-center rounded-3xl mb-12 border border-slate-800">
          <div className="absolute inset-0 opacity-10">
            <img 
              src="https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=1200&q=80" 
              alt="Search project" 
              className="w-full h-full object-cover" 
            />
          </div>
          <div className="absolute -top-32 -right-32 w-80 h-80 rounded-full bg-primary/30 blur-3xl"></div>
          <div className="absolute -bottom-32 -left-32 w-80 h-80 rounded-full bg-primary/10 blur-3xl"></div>

          <div className="relative z-10 max-w-3xl mx-auto">
            <h1 className="text-3xl font-extrabold text-white sm:text-4xl mb-6">
              Tìm kiếm dự án bất động sản
            </h1>
            <form action="/projects" method="GET" className="bg-white/10 backdrop-blur-md rounded-2xl p-2 border border-white/10 flex flex-col sm:flex-row gap-2">
              <div className="flex-grow flex items-center bg-transparent border-b sm:border-b-0 sm:border-r border-white/10 px-4 py-2">
                <i className="fa-solid fa-magnifying-glass text-white/50 mr-3"></i>
                <input 
                  type="text" 
                  name="q" 
                  defaultValue={q}
                  placeholder="Tên dự án, chủ đầu tư..." 
                  className="bg-transparent w-full text-white placeholder-slate-455 focus:outline-none text-sm font-semibold"
                />
              </div>
              <div className="flex-grow flex items-center bg-transparent px-4 py-2">
                <i className="fa-solid fa-location-dot text-white/50 mr-3"></i>
                <input 
                  type="text" 
                  name="city" 
                  defaultValue={city}
                  placeholder="Tỉnh/Thành phố..." 
                  className="bg-transparent w-full text-white placeholder-slate-455 focus:outline-none text-sm font-semibold"
                />
              </div>

              <button type="submit" className="bg-primary hover:bg-primary-hover text-white font-extrabold px-8 py-3 rounded-2xl transition duration-150 shadow-lg shadow-primary/25 cursor-pointer text-sm">
                Tìm kiếm
              </button>
            </form>
          </div>
        </div>

        {/* Filter Tabs */}
        <div className="flex flex-wrap items-center justify-between gap-4 mb-10 pb-6 border-b border-slate-100">
          <div className="flex flex-wrap gap-2">
            <Link 
              href={getFilterUrl()} 
              className={`px-5 py-2.5 rounded-full text-xs font-bold transition duration-150 ${
                !status 
                  ? 'bg-primary text-white shadow-lg shadow-primary/20' 
                  : 'bg-slate-100 text-slate-650 hover:bg-slate-200'
              }`}
            >
              Tất cả dự án
            </Link>
            <Link 
              href={getFilterUrl('selling')} 
              className={`px-5 py-2.5 rounded-full text-xs font-bold transition duration-150 ${
                status === 'selling' 
                  ? 'bg-primary text-white shadow-lg shadow-primary/20' 
                  : 'bg-slate-100 text-slate-650 hover:bg-slate-200'
              }`}
            >
              Đang mở bán
            </Link>
            <Link 
              href={getFilterUrl('upcoming')} 
              className={`px-5 py-2.5 rounded-full text-xs font-bold transition duration-150 ${
                status === 'upcoming' 
                  ? 'bg-primary text-white shadow-lg shadow-primary/20' 
                  : 'bg-slate-100 text-slate-650 hover:bg-slate-200'
              }`}
            >
              Sắp mở bán
            </Link>
            <Link 
              href={getFilterUrl('handed_over')} 
              className={`px-5 py-2.5 rounded-full text-xs font-bold transition duration-150 ${
                status === 'handed_over' 
                  ? 'bg-primary text-white shadow-lg shadow-primary/20' 
                  : 'bg-slate-100 text-slate-650 hover:bg-slate-200'
              }`}
            >
              Đã bàn giao
            </Link>
          </div>
          
          <p className="text-xs font-semibold text-slate-500">
            Hiển thị <span className="text-slate-800 font-bold">{totalCount}</span> dự án
          </p>
        </div>

        {/* Project Directory Grid */}
        {projectsList.length > 0 ? (
          <>
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
              {projectsList.map(proj => (
                <div key={proj.slug} className="bg-white rounded-3xl overflow-hidden border border-slate-100 hover:border-primary/20 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between group h-full">
                  {/* Project Image */}
                  <div className="relative aspect-[16/10] overflow-hidden bg-slate-100">
                    <img src={proj.image} className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt={proj.title} />
                    
                    {/* Status Badge */}
                    <div className="absolute top-4 left-4 z-10">
                      <span className={`inline-block px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-wider text-white ${
                        proj.status === 'selling'
                          ? 'bg-emerald-500'
                          : proj.status === 'upcoming'
                          ? 'bg-orange-500'
                          : 'bg-blue-600'
                      }`}>
                        {proj.status === 'selling' ? 'Đang mở bán' : proj.status === 'upcoming' ? 'Sắp mở bán' : 'Đã bàn giao'}
                      </span>
                    </div>
                  </div>

                  {/* Project Content */}
                  <div className="p-6 flex-grow flex flex-col justify-between">
                    <div>
                      <span className="text-[10px] font-black uppercase text-primary tracking-widest block mb-2">{proj.investor || 'Chủ đầu tư'}</span>
                      <h3 className="text-lg font-bold text-slate-900 group-hover:text-primary transition duration-150 mb-3 line-clamp-1">
                        <Link href={`/projects/${proj.slug}`}>{proj.title}</Link>
                      </h3>
                      <p className="text-slate-500 text-xs mb-5 line-clamp-3 leading-relaxed font-medium">
                        {proj.description}
                      </p>
                    </div>

                    {/* Highlights info */}
                    <div className="pt-5 border-t border-slate-50 grid grid-cols-2 gap-4 text-xs font-semibold text-slate-600">
                      <div className="flex items-center space-x-2">
                        <i className="fa-solid fa-money-bill-wave text-primary"></i>
                        <span className="truncate">{proj.priceRange || 'Liên hệ'}</span>
                      </div>
                      <div className="flex items-center space-x-2">
                        <i className="fa-solid fa-ruler-combined text-primary"></i>
                        <span className="truncate">{proj.scale || 'Đang cập nhật'}</span>
                      </div>
                      <div className="flex items-center space-x-2 col-span-2">
                        <i className="fa-solid fa-location-dot text-primary"></i>
                        <span className="truncate">{proj.location}</span>
                      </div>
                    </div>
                  </div>

                  {/* View details CTA */}
                  <div className="px-6 pb-6 pt-2">
                    <Link 
                      href={`/projects/${proj.slug}`}
                      className="w-full inline-flex items-center justify-center px-4 py-3 border border-slate-100 text-xs font-extrabold rounded-2xl text-slate-700 bg-slate-50 hover:bg-primary hover:text-white hover:border-transparent transition-all duration-200 cursor-pointer"
                    >
                      Xem chi tiết dự án <i className="fa-solid fa-arrow-right ml-2 text-[10px]"></i>
                    </Link>
                  </div>
                </div>
              ))}
            </div>

            {/* Pagination Component */}
            <Pagination currentPage={page} totalPages={totalPages} />
          </>
        ) : (
          <div className="text-center py-16 bg-white rounded-3xl border border-slate-100 shadow-sm p-8">
            <div className="w-16 h-16 rounded-full bg-slate-50 flex items-center justify-center mx-auto mb-4 text-slate-400">
              <i className="fa-solid fa-folder-open text-2xl"></i>
            </div>
            <h3 className="text-sm font-bold text-slate-800 mb-1">Không tìm thấy dự án nào</h3>
            <p className="text-slate-500 text-xs font-semibold">Thử thay đổi từ khóa hoặc bộ lọc tìm kiếm xem sao nhé.</p>
          </div>
        )}

      </div>
    </div>
  )
}
