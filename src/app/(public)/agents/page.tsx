import { prisma } from '@/lib/prisma'
import Link from 'next/link'
import Pagination from '@/components/property/Pagination'

export const dynamic = 'force-dynamic'

interface AgentsPageProps {
  searchParams: Promise<Record<string, string | string[] | undefined>>
}

export default async function AgentsPage({ searchParams }: AgentsPageProps) {
  const resolvedParams = await searchParams
  
  // Extract query filters
  const type = typeof resolvedParams.type === 'string' ? resolvedParams.type : ''
  const q = typeof resolvedParams.q === 'string' ? resolvedParams.q : ''
  const location = typeof resolvedParams.location === 'string' ? resolvedParams.location : ''
  const hasListings = typeof resolvedParams.has_listings === 'string' ? resolvedParams.has_listings : ''
  const verified = typeof resolvedParams.verified === 'string' ? resolvedParams.verified : ''
  const page = Number(resolvedParams.page || 1)

  const limit = type === 'company' ? 8 : 9
  const skip = (page - 1) * limit

  // Query conditions matching Laravel AgentController
  const andFilters: any[] = [
    { role: 'owner' },
    { status: 'active' }
  ]

  if (type === 'company') {
    andFilters.push({
      company: {
        not: null,
        notIn: ['']
      }
    })
  } else if (type === 'agent') {
    andFilters.push({
      OR: [
        { company: null },
        { company: '' }
      ]
    })
  }

  if (q) {
    andFilters.push({
      OR: [
        { name: { contains: q, mode: 'insensitive' } },
        { company: { contains: q, mode: 'insensitive' } },
        { phone: { contains: q, mode: 'insensitive' } }
      ]
    })
  }

  if (location) {
    andFilters.push({
      OR: [
        { addProvince: { contains: location, mode: 'insensitive' } },
        { addDistrict: { contains: location, mode: 'insensitive' } },
        { province: { contains: location, mode: 'insensitive' } },
        { district: { contains: location, mode: 'insensitive' } }
      ]
    })
  }

  if (verified === '1') {
    andFilters.push({
      idNumber: {
        not: null,
        notIn: ['']
      }
    })
  }

  if (hasListings === '1') {
    andFilters.push({
      properties: {
        some: {
          status: 'approved',
          deletedAt: null
        }
      }
    })
  }

  const where = { AND: andFilters }

  // Get total count for counter
  const totalCount = await prisma.user.count({
    where: { role: 'owner', status: 'active' }
  })

  // Paginated queries
  const totalUsers = await prisma.user.count({ where })
  const totalPages = Math.ceil(totalUsers / limit)

  const dbAgents = await prisma.user.findMany({
    where,
    include: {
      _count: {
        select: {
          properties: {
            where: { status: 'approved', deletedAt: null }
          }
        }
      }
    },
    orderBy: {
      createdAt: 'desc'
    },
    skip,
    take: limit
  })

  const agentsList = dbAgents.map(a => ({
    id: a.id.toString(),
    name: a.name,
    email: a.email,
    phone: a.phone,
    avatar: a.avatar,
    company: a.company,
    province: a.province || a.addProvince,
    idNumber: a.idNumber,
    intro: a.intro || (type === 'company' ? 'Doanh nghiệp đối tác uy tín.' : 'Môi giới chuyên nghiệp tại BDS Rental.'),
    propertiesCount: a._count.properties
  }))

  const isCompany = type === 'company'

  return (
    <div className="bg-slate-50 min-h-screen text-slate-800 text-left">
      
      {/* ===================== HERO ===================== */}
      <div className="relative bg-gradient-to-br from-slate-900 via-slate-800 to-blue-950 pt-28 pb-12 overflow-hidden">
        {/* Background pattern */}
        <div className="absolute inset-0 opacity-5">
          <div className="absolute inset-0" style={{
            backgroundImage: `url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E")`
          }}></div>
        </div>

        <div className="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-8">
            {isCompany ? (
              <>
                <div className="inline-flex items-center gap-2 border text-xs font-bold px-4 py-1.5 rounded-full mb-4 uppercase tracking-wider bg-sky-500/20 border-sky-400/40 text-sky-300">
                  <i className="fa-solid fa-building"></i> Đối tác doanh nghiệp
                </div>
                <h1 className="text-4xl md:text-5xl font-black text-white tracking-tight mb-3">
                  Danh Bạ <span className="text-sky-300">Doanh Nghiệp</span> BDS
                </h1>
                <p className="text-slate-350 text-base md:text-lg max-w-2xl mx-auto">
                  Kết nối với các chủ đầu tư, công ty phân phối và đơn vị phát triển dự án bất động sản uy tín hàng đầu.
                </p>
              </>
            ) : (
              <>
                <div className="inline-flex items-center gap-2 border text-xs font-bold px-4 py-1.5 rounded-full mb-4 uppercase tracking-wider bg-sky-500/20 border-sky-400/40 text-sky-300">
                  <i className="fa-solid fa-user-tie"></i> Chuyên viên môi giới
                </div>
                <h1 className="text-4xl md:text-5xl font-black text-white tracking-tight mb-3">
                  Danh Bạ <span className="text-sky-300">Nhà Môi Giới</span> Uy Tín
                </h1>
                <p className="text-slate-355 text-base md:text-lg max-w-2xl mx-auto">
                  Kết nối trực tiếp với các nhà môi giới và chủ nhà chuyên nghiệp, giao dịch an toàn và tối ưu nhất.
                </p>
              </>
            )}
          </div>

          {/* Stats Row */}
          <div className="flex justify-center gap-8 mb-8 text-center">
            <div>
              <div className="text-2xl md:text-3xl font-black text-white">{totalCount}+</div>
              <div className="text-slate-400 text-[10px] md:text-xs font-bold uppercase tracking-wider mt-0.5">
                {isCompany ? 'Doanh nghiệp' : 'Môi giới'}
              </div>
            </div>
            <div className="w-px bg-white/10"></div>
            <div>
              <div className="text-2xl md:text-3xl font-black text-white">100%</div>
              <div className="text-slate-400 text-[10px] md:text-xs font-bold uppercase tracking-wider mt-0.5">Đã xác thực</div>
            </div>
            <div className="w-px bg-white/10"></div>
            <div>
              <div className="text-2xl md:text-3xl font-black text-white">63+</div>
              <div className="text-slate-400 text-[10px] md:text-xs font-bold uppercase tracking-wider mt-0.5">Tỉnh thành</div>
            </div>
          </div>

          {/* Search Bar */}
          <form action="/agents" method="GET" className="max-w-4xl mx-auto">
            {type && <input type="hidden" name="type" value={type} />}
            <div className="bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col md:flex-row">
              <div className="flex-grow flex items-center px-5 py-4 border-b md:border-b-0 md:border-r border-slate-100">
                <i className="fa-solid fa-magnifying-glass text-slate-400 mr-3 text-sm"></i>
                <input
                  type="text"
                  name="q"
                  defaultValue={q}
                  placeholder={isCompany ? "Tìm tên doanh nghiệp, công ty, chủ đầu tư..." : "Tìm tên môi giới, công ty..."}
                  className="w-full text-slate-800 placeholder-slate-400 focus:outline-none text-sm font-semibold"
                />
              </div>
              <div className="flex items-center px-5 py-4 border-b md:border-b-0 md:border-r border-slate-100 min-w-[220px]">
                <i className="fa-solid fa-location-dot text-slate-400 mr-3 text-sm"></i>
                <input
                  type="text"
                  name="location"
                  defaultValue={location}
                  placeholder="Khu vực hoạt động..."
                  className="w-full text-slate-800 placeholder-slate-400 focus:outline-none text-sm font-semibold"
                />
              </div>
              <button type="submit" className="bg-primary hover:bg-primary-hover text-white font-extrabold px-8 py-4 transition duration-150 whitespace-nowrap text-sm cursor-pointer">
                <i className="fa-solid fa-search mr-2"></i> Tìm kiếm
              </button>
            </div>
          </form>
        </div>
      </div>

      {/* ===================== TAB NAVIGATION ===================== */}
      <div className="bg-white border-b border-slate-100 shadow-sm sticky top-[70px] z-30">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex items-center">
            {/* Tab: Nhà Môi Giới */}
            <Link 
              href="/agents?type=agent"
              className={`flex items-center gap-2 px-7 py-4 text-sm font-bold border-b-2 transition-colors duration-150 ${
                type !== 'company'
                  ? 'border-primary text-primary bg-primary/5'
                  : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50'
              }`}
            >
              <i className={`fa-solid fa-user-tie ${type !== 'company' ? 'text-primary' : 'text-slate-400'}`}></i>
              Nhà Môi Giới
            </Link>

            {/* Divider */}
            <div className="w-px h-5 bg-slate-200 mx-2 self-center"></div>

            {/* Tab: Doanh Nghiệp */}
            <Link 
              href="/agents?type=company"
              className={`flex items-center gap-2 px-7 py-4 text-sm font-bold border-b-2 transition-colors duration-150 ${
                type === 'company'
                  ? 'border-primary text-primary bg-primary/5'
                  : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50'
              }`}
            >
              <i className={`fa-solid fa-building ${type === 'company' ? 'text-primary' : 'text-slate-400'}`}></i>
              Doanh Nghiệp
            </Link>
          </div>
        </div>
      </div>

      {/* ===================== MAIN CONTENT ===================== */}
      <div className="bg-slate-50 min-h-screen">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
          <div className="flex flex-col lg:flex-row gap-8">

            {/* ===== SIDEBAR TRÁI ===== */}
            <aside className="hidden lg:block w-64 flex-shrink-0">
              <div className="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden sticky top-36">
                <div className="bg-slate-50 px-5 py-4 border-b border-slate-100">
                  <h3 className="text-sm font-black text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <i className="fa-solid fa-filter text-primary text-xs"></i> Bộ lọc nhanh
                  </h3>
                </div>

                <form action="/agents" method="GET" className="p-5 space-y-5">
                  {type && <input type="hidden" name="type" value={type} />}
                  {q && <input type="hidden" name="q" value={q} />}

                  {/* Khu vực */}
                  <div>
                    <label className="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">
                      <i className="fa-solid fa-map-marker-alt text-primary mr-1"></i> Khu vực
                    </label>
                    <select 
                      name="location" 
                      defaultValue={location}
                      className="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary bg-slate-50 cursor-pointer font-semibold"
                    >
                      <option value="">Tất cả khu vực</option>
                      <option value="Hà Nội">Hà Nội</option>
                      <option value="Hồ Chí Minh">TP. Hồ Chí Minh</option>
                      <option value="Đà Nẵng">Đà Nẵng</option>
                      <option value="Bình Dương">Bình Dương</option>
                      <option value="Đồng Nai">Đồng Nai</option>
                      <option value="Cần Thơ">Cần Thơ</option>
                    </select>
                  </div>

                  {/* Số tin đăng */}
                  <div>
                    <label className="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">
                      <i className="fa-solid fa-list text-primary mr-1"></i> Số tin đăng
                    </label>
                    <div className="space-y-1.5">
                      <label className="flex items-center gap-2.5 cursor-pointer group">
                        <input 
                          type="radio" 
                          name="has_listings" 
                          value=""
                          defaultChecked={hasListings !== '1'}
                          className="accent-primary w-4 h-4 cursor-pointer"
                        />
                        <span className="text-sm text-slate-600 group-hover:text-slate-900 font-medium">Tất cả</span>
                      </label>
                      <label className="flex items-center gap-2.5 cursor-pointer group">
                        <input 
                          type="radio" 
                          name="has_listings" 
                          value="1"
                          defaultChecked={hasListings === '1'}
                          className="accent-primary w-4 h-4 cursor-pointer"
                        />
                        <span className="text-sm text-slate-600 group-hover:text-slate-900 font-medium">Có tin đăng</span>
                      </label>
                    </div>
                  </div>

                  {/* Xác thực */}
                  <div>
                    <label className="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">
                      <i className="fa-solid fa-shield-halved text-primary mr-1"></i> Xác thực
                    </label>
                    <div className="space-y-1.5">
                      <label className="flex items-center gap-2.5 cursor-pointer group">
                        <input 
                          type="checkbox" 
                          name="verified" 
                          value="1"
                          defaultChecked={verified === '1'}
                          className="accent-primary w-4 h-4 rounded cursor-pointer"
                        />
                        <span className="text-sm text-slate-600 group-hover:text-slate-900 font-medium">Đã xác thực danh tính</span>
                      </label>
                    </div>
                  </div>

                  <button type="submit" className="w-full bg-primary hover:bg-primary-hover text-white text-sm font-bold py-2.5 rounded-xl transition cursor-pointer">
                    Áp dụng bộ lọc
                  </button>

                  {(location || hasListings || verified || q) && (
                    <Link 
                      href={`/agents${type ? `?type=${type}` : ''}`}
                      className="block text-center text-xs text-slate-400 hover:text-primary font-semibold mt-1"
                    >
                      <i className="fa-solid fa-rotate-left mr-1"></i> Xóa bộ lọc
                    </Link>
                  )}
                </form>
              </div>
            </aside>

            {/* ===== DANH SÁCH ===== */}
            <div className="flex-grow min-w-0">

              {/* Header row */}
              <div className="flex items-center justify-between mb-6">
                <div>
                  <h2 className="text-xl font-black text-slate-900 flex items-center">
                    {isCompany ? (
                      <>
                        <i className="fa-solid fa-building text-primary mr-2"></i>Doanh nghiệp đối tác
                      </>
                    ) : (
                      <>
                        <i className="fa-solid fa-user-tie text-primary mr-2"></i>Chuyên viên môi giới
                      </>
                    )}
                  </h2>
                  <p className="text-sm text-slate-500 mt-0.5">
                    Tìm thấy <span className="font-bold text-slate-800">{totalUsers}</span>{' '}
                    {isCompany ? 'doanh nghiệp' : 'môi giới'}
                    {location && <> tại <span className="text-primary font-semibold">{location}</span></>}
                  </p>
                </div>

                {/* Active filters badges */}
                <div className="flex items-center gap-2 flex-wrap justify-end">
                  {q && (
                    <span className="inline-flex items-center gap-1 bg-primary/10 text-primary text-xs font-bold px-3 py-1 rounded-full">
                      <i className="fa-solid fa-search text-[10px]"></i> "{q}"
                    </span>
                  )}
                  {location && (
                    <span className="inline-flex items-center gap-1 bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1 rounded-full">
                      <i className="fa-solid fa-location-dot text-[10px]"></i> {location}
                    </span>
                  )}
                </div>
              </div>

              {agentsList.length === 0 ? (
                <div className="text-center py-20 bg-white rounded-2xl border border-slate-100 shadow-sm p-8">
                  <div className="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4 text-slate-400">
                    <i className="fa-solid fa-building-circle-xmark text-2xl"></i>
                  </div>
                  <h3 className="text-lg font-bold text-slate-800 mb-1">Không tìm thấy kết quả</h3>
                  <p className="text-slate-500 text-sm">Thử thay đổi từ khóa hoặc bộ lọc tìm kiếm.</p>
                  <Link 
                    href={`/agents${type ? `?type=${type}` : ''}`}
                    className="inline-flex items-center gap-2 mt-4 text-sm text-primary font-bold hover:underline"
                  >
                    <i className="fa-solid fa-rotate-left"></i> Xem tất cả
                  </Link>
                </div>
              ) : (
                <>
                  {/* Company mode: HORIZONTAL CARDS */}
                  {isCompany ? (
                    <div className="space-y-4">
                      {agentsList.map((agent, index) => {
                        const avatarUrl = agent.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(agent.company || agent.name)}&background=0077bb&color=fff&bold=true&size=128`
                        
                        return (
                          <div key={agent.id} className="group bg-white rounded-2xl border border-slate-100 hover:border-primary/20 shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden">
                            <div className="flex flex-col sm:flex-row items-stretch">
                              {/* Logo / Avatar block */}
                              <Link 
                                href={`/agents/${agent.id}`}
                                className="flex-shrink-0 w-full sm:w-36 bg-gradient-to-br from-slate-50 to-slate-100 flex items-center justify-center p-5 border-r border-slate-100 group-hover:from-primary/5 group-hover:to-blue-50 transition-colors"
                              >
                                <img
                                  src={avatarUrl}
                                  alt={agent.company || agent.name}
                                  className="w-20 h-20 rounded-2xl object-cover shadow-sm border-2 border-white"
                                />
                              </Link>

                              {/* Info block */}
                              <div className="flex-1 p-5 flex flex-col justify-between min-w-0">
                                <div>
                                  <div className="flex flex-col sm:flex-row items-start justify-between gap-3 mb-1">
                                    <div className="min-w-0">
                                      {/* Featured badge for first 3 on page 1 */}
                                      {index < 3 && page === 1 && (
                                        <span className="inline-flex items-center gap-1 bg-amber-100 text-amber-700 text-[10px] font-black px-2 py-0.5 rounded-full uppercase tracking-wider mb-1">
                                          <i className="fa-solid fa-star text-[8px]"></i> Nổi bật
                                        </span>
                                      )}
                                      <h3 className="text-base font-extrabold text-slate-900 group-hover:text-primary transition line-clamp-1">
                                        <Link href={`/agents/${agent.id}`}>
                                          {agent.company || agent.name}
                                        </Link>
                                      </h3>
                                      <p className="text-xs text-slate-500 font-medium mt-0.5">
                                        <i className="fa-solid fa-user mr-1 text-slate-400"></i>Đại diện: {agent.name}
                                      </p>
                                    </div>

                                    {/* KYC badge */}
                                    {agent.idNumber && (
                                      <span className="flex-shrink-0 inline-flex items-center gap-1 bg-blue-100 text-blue-700 text-[10px] font-bold px-2.5 py-1 rounded-full whitespace-nowrap">
                                        <i className="fa-solid fa-shield-halved text-[10px]"></i> Đã xác thực
                                      </span>
                                    )}
                                  </div>

                                  {/* Meta info row */}
                                  <div className="flex flex-wrap items-center gap-x-4 gap-y-1 mt-2 text-xs text-slate-500">
                                    <span className="flex items-center gap-1">
                                      <i className="fa-solid fa-location-dot text-primary/70"></i>
                                      {agent.province || 'Toàn quốc'}
                                    </span>
                                    <span className="flex items-center gap-1">
                                      <i className="fa-solid fa-newspaper text-primary/70"></i>
                                      {agent.propertiesCount} tin đăng
                                    </span>
                                    {agent.phone && (
                                      <span className="flex items-center gap-1">
                                        <i className="fa-solid fa-phone text-primary/70"></i>
                                        {agent.phone}
                                      </span>
                                    )}
                                  </div>
                                </div>

                                {/* CTA Buttons */}
                                <div className="flex items-center gap-2 mt-4 flex-wrap">
                                  <Link 
                                    href={`/agents/${agent.id}`}
                                    className="inline-flex items-center gap-1.5 px-4 py-2 bg-primary text-white text-xs font-bold rounded-xl hover:bg-primary-hover transition shadow-sm shadow-primary/20 cursor-pointer"
                                  >
                                    <i className="fa-solid fa-building"></i> Xem trang DN
                                  </Link>
                                  {agent.phone && (
                                    <>
                                      <a 
                                        href={`tel:${agent.phone}`}
                                        className="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition cursor-pointer"
                                      >
                                        <i className="fa-solid fa-phone"></i> Gọi ngay
                                      </a>
                                      <a 
                                        href={`https://zalo.me/${agent.phone}`} 
                                        target="_blank" 
                                        rel="noopener noreferrer"
                                        className="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-xs font-bold rounded-xl transition cursor-pointer"
                                      >
                                        <i className="fa-solid fa-comment"></i> Zalo
                                      </a>
                                    </>
                                  )}
                                </div>
                              </div>

                              {/* Right stats block */}
                              <div className="hidden md:flex flex-col items-center justify-center w-28 bg-slate-50 border-l border-slate-100 p-4 gap-3 group-hover:bg-primary/5 transition-colors">
                                <div className="text-center">
                                  <div className="text-2xl font-black text-slate-900">{agent.propertiesCount}</div>
                                  <div className="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tin đăng</div>
                                </div>
                                <div className="w-8 border-t border-slate-200"></div>
                                <div className="text-center">
                                  <div className="text-lg font-black text-green-600">✓</div>
                                  <div className="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Uy tín</div>
                                </div>
                              </div>
                            </div>
                          </div>
                        )
                      })}
                    </div>
                  ) : (
                    /* Agent mode: VERTICAL GRID CARDS */
                    <div className="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
                      {agentsList.map(agent => {
                        const avatarUrl = agent.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(agent.name)}&background=0077bb&color=fff&bold=true&size=128`
                        
                        return (
                          <div key={agent.id} className="bg-white rounded-2xl border border-slate-100 hover:border-primary/20 shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden group flex flex-col justify-between h-full">
                            <div>
                              {/* Top banner */}
                              <div className="h-16 bg-gradient-to-r from-slate-800 to-slate-700 relative">
                                <div className="absolute inset-0 opacity-20" style={{
                                  backgroundImage: `url("data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23fff' fill-opacity='1' fill-rule='evenodd'%3E%3Cpath d='M0 40L40 0H20L0 20M40 40V20L20 40'/%3E%3C/g%3E%3C/svg%3E")`
                                }}></div>
                              </div>

                              <div className="px-5 pb-2">
                                {/* Avatar overlapping banner */}
                                <div className="relative -mt-8 mb-3 flex justify-center">
                                  <div className="relative">
                                    <img
                                      src={avatarUrl}
                                      alt={agent.name}
                                      className="w-16 h-16 rounded-full object-cover border-4 border-white shadow-md"
                                    />
                                    {agent.idNumber && (
                                      <span className="absolute bottom-0 right-0 w-5 h-5 bg-blue-500 rounded-full border-2 border-white flex items-center justify-center" title="Đã xác thực">
                                        <i className="fa-solid fa-check text-white text-[8px]"></i>
                                      </span>
                                    )}
                                  </div>
                                </div>

                                {/* Name */}
                                <div className="text-center mb-3">
                                  <h3 className="font-extrabold text-slate-900 group-hover:text-primary transition text-sm line-clamp-1">
                                    <Link href={`/agents/${agent.id}`}>{agent.name}</Link>
                                  </h3>
                                  <p className="text-xs text-primary font-bold mt-0.5 truncate">
                                    {agent.company || 'Môi giới độc lập'}
                                  </p>
                                  <p className="text-xs text-slate-400 mt-0.5 truncate">
                                    <i className="fa-solid fa-location-dot mr-1"></i>
                                    {agent.province ? agent.province.replace(/Tỉnh |Thành phố /, '') : 'Toàn quốc'}
                                  </p>
                                </div>

                                {/* Stats */}
                                <div className="grid grid-cols-2 gap-2 mb-4">
                                  <div className="bg-slate-50 rounded-xl p-2 text-center">
                                    <div className="text-base font-black text-slate-900">{agent.propertiesCount}</div>
                                    <div className="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tin đăng</div>
                                  </div>
                                  <div className="bg-slate-50 rounded-xl p-2 text-center">
                                    <div className="text-base font-black text-green-600">✓</div>
                                    <div className="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Uy tín</div>
                                  </div>
                                </div>
                              </div>
                            </div>

                            {/* CTA */}
                            <div className="px-5 pb-5 space-y-2">
                              <Link 
                                href={`/agents/${agent.id}`}
                                className="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl bg-primary hover:bg-primary-hover text-white text-xs font-bold transition shadow-sm shadow-primary/20 cursor-pointer"
                              >
                                <i className="fa-solid fa-id-card"></i> Xem trang cá nhân
                              </Link>
                              <div className="flex gap-2">
                                <a 
                                  href={`tel:${agent.phone}`}
                                  className="flex-1 inline-flex items-center justify-center gap-1 py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition cursor-pointer"
                                >
                                  <i className="fa-solid fa-phone"></i> Gọi
                                </a>
                                {agent.phone && (
                                  <a 
                                    href={`https://zalo.me/${agent.phone}`}
                                    target="_blank" 
                                    rel="noopener noreferrer"
                                    className="flex-1 inline-flex items-center justify-center gap-1 py-2 px-3 rounded-xl bg-blue-500 hover:bg-blue-600 text-white text-xs font-bold transition cursor-pointer"
                                  >
                                    <i className="fa-solid fa-comment"></i> Zalo
                                  </a>
                                )}
                              </div>
                            </div>
                          </div>
                        )
                      })}
                    </div>
                  )}

                  {/* Pagination */}
                  <Pagination currentPage={page} totalPages={totalPages} />
                </>
              )}
            </div>
          </div>
        </div>
      </div>
    </div>
  )
}
