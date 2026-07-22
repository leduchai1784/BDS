import { prisma } from '@/lib/prisma'
import { notFound } from 'next/navigation'
import Link from 'next/link'
import AgentPropertiesTabs from '@/components/agent/AgentPropertiesTabs'

export const dynamic = 'force-dynamic'

interface AgentDetailPageProps {
  params: Promise<{ id: string }>
}

export default async function AgentDetailPage({ params }: AgentDetailPageProps) {
  const resolvedParams = await params
  const agentId = Number(resolvedParams.id)

  const agent = await prisma.user.findUnique({
    where: { id: BigInt(agentId) }
  })

  if (!agent || !['owner', 'agent'].includes(agent.role) || agent.status !== 'active') {
    notFound()
  }

  // Fetch properties posted by this agent (both sale and rent)
  const dbProperties = await prisma.property.findMany({
    where: {
      ownerId: agentId,
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
    id: p.id.toString(),
    title: p.title,
    price: Number(p.price),
    priceLabel: p.priceLabel || 'Liên hệ',
    area: Number(p.area || 0),
    bedroom: Number(p.bedroom || 0),
    bathroom: Number(p.bathroom || 0),
    floors: p.floors,
    address: p.address,
    district: p.district || '',
    city: p.city || '',
    isVip: !!p.isVip,
    isNew: !!p.isNew,
    propertyType: p.propertyType,
    imagePath: p.propertyImages?.[0]?.imagePath || null,
    transactionType: p.transactionType
  }))

  const saleProperties = mappedProperties.filter(p => p.transactionType === 'sale')
  const rentProperties = mappedProperties.filter(p => p.transactionType === 'rent')

  const isCompany = !!agent.company
  const displayName = agent.company || agent.name
  const avatarUrl = agent.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(displayName)}&background=0077bb&color=fff&bold=true&size=256`

  const provName = agent.province || agent.addProvince
  const distName = agent.district || agent.addDistrict

  return (
    <div className="bg-slate-50 min-h-screen text-slate-800 text-left">
      
      {/* ========== HERO PROFILE BANNER ========== */}
      <div className="relative bg-gradient-to-br from-slate-900 via-slate-800 to-blue-950 pt-24 pb-0 overflow-hidden">
        {/* Background pattern */}
        <div className="absolute inset-0 opacity-5">
          <div className="absolute inset-0" style={{
            backgroundImage: `url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E")`
          }}></div>
        </div>

        <div className="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 pb-16">
          {/* Breadcrumb */}
          <nav className="flex items-center gap-2 text-sm text-slate-400 font-semibold mb-8">
            <Link href="/" className="hover:text-white transition">Trang chủ</Link>
            <i className="fa-solid fa-chevron-right text-[10px]"></i>
            <Link 
              href={`/agents${isCompany ? '?type=company' : ''}`}
              className="hover:text-white transition"
            >
              {isCompany ? 'Doanh nghiệp' : 'Môi giới'}
            </Link>
            <i className="fa-solid fa-chevron-right text-[10px]"></i>
            <span className="text-white truncate max-w-xs">{displayName}</span>
          </nav>

          {/* Profile Header */}
          <div className="flex flex-col md:flex-row items-center md:items-end gap-6">
            {/* Logo / Avatar */}
            <div className="relative flex-shrink-0">
              <img
                src={avatarUrl}
                alt={displayName}
                className={`w-28 h-28 md:w-32 md:h-32 object-cover border-4 border-white/20 shadow-2xl ${
                  isCompany ? 'rounded-2xl' : 'rounded-full'
                }`}
              />
              {agent.idNumber && (
                <span className="absolute -bottom-2 -right-2 w-8 h-8 bg-blue-500 rounded-full border-3 border-white flex items-center justify-center shadow-lg" title="Đã xác thực danh tính">
                  <i className="fa-solid fa-check text-white text-xs"></i>
                </span>
              )}
            </div>

            {/* Name & Meta */}
            <div className="flex-grow text-center md:text-left">
              <div className="flex flex-wrap items-center justify-center md:justify-start gap-2 mb-2">
                {isCompany ? (
                  <span className="inline-flex items-center gap-1 border text-xs font-black px-3 py-1 rounded-full uppercase tracking-wider bg-sky-500/20 border-sky-400/40 text-sky-300">
                    <i className="fa-solid fa-building text-[10px]"></i> Doanh nghiệp
                  </span>
                ) : (
                  <span className="inline-flex items-center gap-1 bg-blue-500/30 text-blue-300 border border-blue-400/40 text-xs font-black px-3 py-1 rounded-full uppercase tracking-wider">
                    <i className="fa-solid fa-user-tie text-[10px]"></i> Nhà môi giới
                  </span>
                )}
                {agent.idNumber && (
                  <span className="inline-flex items-center gap-1 bg-blue-600/30 text-blue-300 border border-blue-400/30 text-xs font-bold px-3 py-1 rounded-full">
                    <i className="fa-solid fa-shield-halved text-[10px]"></i> Đã xác thực danh tính
                  </span>
                )}
              </div>

              <h1 className="text-2xl md:text-3xl font-black text-white mb-1">
                {displayName}
              </h1>
              {isCompany && (
                <p className="text-slate-400 text-sm font-medium">
                  <i className="fa-solid fa-user mr-1.5"></i>Đại diện: <span className="text-slate-200 font-semibold">{agent.name}</span>
                </p>
              )}
              <div className="flex flex-wrap items-center justify-center md:justify-start gap-4 mt-3 text-sm text-slate-400">
                {provName && (
                  <span className="flex items-center gap-1.5">
                    <i className="fa-solid fa-location-dot text-primary/80"></i>
                    {distName ? `${distName}, ` : ''}{provName}
                  </span>
                )}
                <span className="flex items-center gap-1.5">
                  <i className="fa-solid fa-newspaper text-primary/80"></i>
                  {mappedProperties.length} tin đăng đang hoạt động
                </span>
              </div>
            </div>

            {/* Action buttons (desktop) */}
            <div className="flex-shrink-0 flex flex-col gap-2 min-w-[180px] w-full md:w-auto mt-4 md:mt-0">
              <a 
                href={`tel:${agent.phone || ''}`}
                className="inline-flex items-center justify-center gap-2 py-3 px-6 bg-primary hover:bg-primary-hover text-white text-sm font-bold rounded-xl transition shadow-lg shadow-primary/30 cursor-pointer"
              >
                <i className="fa-solid fa-phone"></i> {agent.phone || 'Gọi điện'}
              </a>
              {agent.phone && (
                <a 
                  href={`https://zalo.me/${agent.phone}`} 
                  target="_blank" 
                  rel="noopener noreferrer"
                  className="inline-flex items-center justify-center gap-2 py-3 px-6 bg-blue-500 hover:bg-blue-600 text-white text-sm font-bold rounded-xl transition cursor-pointer"
                >
                  <i className="fa-solid fa-comment"></i> Chat Zalo
                </a>
              )}
            </div>
          </div>
        </div>

        {/* Stats bar at bottom of hero */}
        <div className="relative bg-white/5 backdrop-blur-sm border-t border-white/10">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="flex divide-x divide-white/10 text-center">
              <div className="flex-1 py-4">
                <div className="text-xl font-black text-white">{saleProperties.length}</div>
                <div className="text-[10px] md:text-xs text-slate-400 font-semibold mt-0.5 uppercase tracking-wider">Tin bán</div>
              </div>
              <div className="flex-1 py-4">
                <div className="text-xl font-black text-white">{rentProperties.length}</div>
                <div className="text-[10px] md:text-xs text-slate-400 font-semibold mt-0.5 uppercase tracking-wider">Tin cho thuê</div>
              </div>
              <div className="flex-1 py-4">
                <div className="text-xl font-black text-white">{mappedProperties.length}</div>
                <div className="text-[10px] md:text-xs text-slate-400 font-semibold mt-0.5 uppercase tracking-wider">Tổng tin đăng</div>
              </div>
              <div className="flex-1 py-4">
                <div className="text-xl font-black text-green-400">✓</div>
                <div className="text-[10px] md:text-xs text-slate-400 font-semibold mt-0.5 uppercase tracking-wider">Uy tín</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* ========== MAIN CONTENT ========== */}
      <div className="bg-slate-50 min-h-screen">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
          <div className="grid grid-cols-1 lg:grid-cols-4 gap-8">

            {/* ===== LEFT SIDEBAR ===== */}
            <div className="lg:col-span-1 space-y-5">
              
              {/* Contact Card */}
              <div className="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden text-left">
                <div className="bg-slate-50 px-5 py-3.5 border-b border-slate-100">
                  <h3 className="text-xs font-black text-slate-700 uppercase tracking-wider flex items-center">
                    <i className="fa-solid fa-address-card text-primary mr-1.5"></i>Thông tin liên hệ
                  </h3>
                </div>
                <div className="p-5 space-y-4">
                  {agent.phone && (
                    <div className="flex items-center gap-3">
                      <div className="w-9 h-9 rounded-xl bg-primary/10 flex items-center justify-center text-primary flex-shrink-0">
                        <i className="fa-solid fa-phone text-sm"></i>
                      </div>
                      <div>
                        <div className="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Điện thoại</div>
                        <a href={`tel:${agent.phone}`} className="text-sm font-extrabold text-slate-900 hover:text-primary transition">{agent.phone}</a>
                      </div>
                    </div>
                  )}
                  <div className="flex items-center gap-3">
                    <div className="w-9 h-9 rounded-xl bg-primary/10 flex items-center justify-center text-primary flex-shrink-0">
                      <i className="fa-solid fa-envelope text-sm"></i>
                    </div>
                    <div className="min-w-0">
                      <div className="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Email</div>
                      <span className="text-sm font-semibold text-slate-800 truncate block select-all" title={agent.email}>{agent.email}</span>
                    </div>
                  </div>
                  <div className="flex items-center gap-3">
                    <div className="w-9 h-9 rounded-xl bg-primary/10 flex items-center justify-center text-primary flex-shrink-0">
                      <i className="fa-solid fa-location-dot text-sm"></i>
                    </div>
                    <div>
                      <div className="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Khu vực</div>
                      <span className="text-sm font-semibold text-slate-800">
                        {agent.province || agent.addProvince || 'Toàn quốc'}
                      </span>
                    </div>
                  </div>

                  <div className="pt-2 space-y-2">
                    <a 
                      href={`tel:${agent.phone || ''}`}
                      className="w-full inline-flex items-center justify-center gap-2 py-2.5 rounded-xl bg-primary hover:bg-primary-hover text-white text-sm font-bold transition shadow shadow-primary/20 cursor-pointer"
                    >
                      <i className="fa-solid fa-phone"></i> Gọi điện ngay
                    </a>
                    {agent.phone && (
                      <a 
                        href={`https://zalo.me/${agent.phone}`} 
                        target="_blank" 
                        rel="noopener noreferrer"
                        className="w-full inline-flex items-center justify-center gap-2 py-2.5 rounded-xl bg-blue-500 hover:bg-blue-600 text-white text-sm font-bold transition cursor-pointer"
                      >
                        <i className="fa-solid fa-comment"></i> Chat qua Zalo
                      </a>
                    )}
                  </div>
                </div>
              </div>

              {/* Intro panel */}
              {agent.intro && (
                <div className="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 text-left">
                  <h3 className="text-xs font-black text-slate-700 uppercase tracking-wider mb-3 flex items-center">
                    <i className="fa-solid fa-circle-info text-primary mr-1.5"></i>Giới thiệu
                  </h3>
                  <p className="text-slate-650 text-sm leading-relaxed whitespace-pre-line">{agent.intro}</p>
                </div>
              )}

              {/* Back button */}
              <Link 
                href={`/agents${isCompany ? '?type=company' : ''}`}
                className="flex items-center gap-2 text-sm text-slate-500 hover:text-primary font-semibold transition mt-4"
              >
                <i className="fa-solid fa-arrow-left"></i>
                Quay lại danh sách {isCompany ? 'doanh nghiệp' : 'môi giới'}
              </Link>
            </div>

            {/* ===== LISTINGS PANEL ===== */}
            <div className="lg:col-span-3">
              <AgentPropertiesTabs 
                saleProperties={saleProperties} 
                rentProperties={rentProperties} 
                isCompany={isCompany}
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  )
}
