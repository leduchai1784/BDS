import { prisma } from '@/lib/prisma'
import Link from 'next/link'

export const dynamic = 'force-dynamic'

export default async function ProjectsPage() {
  const dbProjects = await prisma.project.findMany({
    orderBy: { createdAt: 'desc' }
  })

  // Map BigInt to Numbers / JSON parses
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

  return (
    <div className="bg-slate-50 min-h-screen pt-28 pb-16 text-slate-800 text-left">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {/* Breadcrumbs */}
        <nav className="flex text-xs font-semibold text-slate-500 mb-6 space-x-2">
          <Link href="/" className="hover:text-primary transition">Trang chủ</Link>
          <span>/</span>
          <span className="text-slate-850">Dự án bất động sản</span>
        </nav>

        {/* Page Title */}
        <div className="mb-10">
          <span className="text-xs font-bold text-primary tracking-widest uppercase mb-2 block">Featured Projects</span>
          <h1 className="text-3xl font-extrabold text-slate-900 leading-tight">Danh sách Dự án nổi bật</h1>
        </div>

        {/* Projects Grid */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
          {projectsList.length > 0 ? (
            projectsList.map(proj => (
              <div key={proj.slug} className="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm hover:shadow-md transition flex flex-col justify-between">
                <div>
                  <div className="w-full h-48 overflow-hidden bg-slate-100 relative">
                    <img src={proj.image} className="w-full h-full object-cover" alt={proj.title} />
                  </div>
                  <div className="p-5 space-y-2">
                    <span className={`inline-block px-2 py-0.5 rounded-md text-[8px] font-black uppercase ${
                      proj.status === 'selling'
                        ? 'bg-emerald-50 text-emerald-650'
                        : proj.status === 'upcoming'
                        ? 'bg-amber-50 text-amber-605'
                        : 'bg-slate-100 text-slate-550'
                    }`}>
                      {proj.status === 'selling' ? 'Đang mở bán' : proj.status === 'upcoming' ? 'Sắp mở bán' : 'Đã bàn giao'}
                    </span>
                    <h3 className="text-sm font-bold text-slate-800 line-clamp-1">{proj.title}</h3>
                    <p className="text-[10px] text-slate-400 font-semibold"><i className="fa-solid fa-location-dot mr-1" />{proj.location}</p>
                    <p className="text-[11px] text-slate-500 leading-relaxed line-clamp-2 font-medium">{proj.description}</p>
                  </div>
                </div>

                <div className="px-5 pb-5 pt-3 border-t border-slate-50 mt-2 text-xs font-semibold flex items-center justify-between">
                  <div>
                    <span className="block text-[8px] uppercase tracking-wider text-slate-400">Khoảng giá</span>
                    <span className="text-primary font-black text-xs">{proj.priceRange || 'Thương lượng'}</span>
                  </div>
                  <Link 
                    href={`/projects/${proj.slug}`}
                    className="px-4 py-2 bg-slate-100 hover:bg-primary hover:text-white rounded-xl text-[10px] font-black transition cursor-pointer"
                  >
                    Xem chi tiết
                  </Link>
                </div>
              </div>
            ))
          ) : (
            <div className="col-span-3 py-16 text-center text-slate-400 text-sm font-semibold bg-white rounded-3xl border border-slate-100 shadow-inner p-8">
              Chưa có dữ liệu dự án nào.
            </div>
          )}
        </div>

      </div>
    </div>
  )
}
