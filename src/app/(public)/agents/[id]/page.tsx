import { prisma } from '@/lib/prisma'
import { notFound } from 'next/navigation'
import Link from 'next/link'
import PropertyCard from '@/components/property/PropertyCard'

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

  if (!agent) {
    notFound()
  }

  // Fetch properties posted by this agent
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
          <Link href="/agents" className="hover:text-primary transition">Danh sách Môi giới / Chủ nhà</Link>
          <span>/</span>
          <span className="text-slate-850 truncate max-w-xs">{agent.name}</span>
        </nav>

        {/* Agent Profile Box */}
        <div className="bg-white rounded-3xl border border-slate-100 shadow-xl overflow-hidden p-6 sm:p-10 flex flex-col md:flex-row gap-8 items-start">
          {/* Avatar Circle */}
          <div className="w-28 h-28 rounded-2xl bg-slate-50 border border-slate-200 overflow-hidden flex-shrink-0 flex items-center justify-center mx-auto md:mx-0">
            {agent.avatar ? (
              <img src={agent.avatar} className="w-full h-full object-cover" alt={agent.name} />
            ) : (
              <i className="fa-regular fa-user text-slate-300 text-4xl" />
            )}
          </div>

          {/* Details */}
          <div className="flex-grow space-y-4 text-center md:text-left">
            <div>
              <h1 className="text-xl sm:text-2xl font-black text-slate-900 leading-none">{agent.name}</h1>
              <span className="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mt-1.5">Chủ nhà & Môi giới BDS Rental</span>
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs font-semibold text-slate-650 max-w-xl">
              <div className="flex items-center gap-2 justify-center md:justify-start">
                <i className="fa-solid fa-envelope text-slate-400" />
                <span className="select-all">{agent.email}</span>
              </div>
              <div className="flex items-center gap-2 justify-center md:justify-start">
                <i className="fa-solid fa-phone text-slate-400" />
                <span className="select-all">{agent.phone || 'Chưa cung cấp'}</span>
              </div>
              <div className="flex items-center gap-2 justify-center md:justify-start">
                <i className="fa-solid fa-cake-candles text-slate-400" />
                <span>Ngày sinh: {agent.dob || '—'}</span>
              </div>
            </div>

            <div className="border-t border-slate-100 pt-4 space-y-2">
              <span className="block text-[9px] uppercase tracking-wider text-slate-400 font-bold">Giới thiệu bản thân</span>
              <p className="text-xs text-slate-500 font-medium leading-relaxed max-w-3xl">
                {agent.intro || 'Chủ nhà uy tín, hỗ trợ đầy đủ thủ tục xem phòng trọ, căn hộ trực tiếp, cam kết pháp lý sạch sẽ rõ ràng.'}
              </p>
            </div>
          </div>
        </div>

        {/* Listings posted by this Agent */}
        <section className="space-y-6">
          <div className="border-b border-slate-200 pb-3">
            <h2 className="text-lg font-black text-slate-850">Danh sách tin rao cho thuê đang hoạt động ({mappedProperties.length})</h2>
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
              <p className="text-slate-400 text-xs font-bold">Hiện tại chủ nhà này chưa có tin rao cho thuê nào được phê duyệt.</p>
            </div>
          )}
        </section>

      </div>
    </div>
  )
}
