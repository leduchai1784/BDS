import { prisma } from '@/lib/prisma'
import Link from 'next/link'

export const dynamic = 'force-dynamic'

export default async function AgentsPage() {
  
  // Find all active owners who have properties
  const dbAgents = await prisma.user.findMany({
    where: { role: 'owner' },
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
    }
  })

  const agentsList = dbAgents.map(a => ({
    id: a.id.toString(),
    name: a.name,
    email: a.email,
    phone: a.phone,
    avatar: a.avatar,
    intro: a.intro || 'Môi giới chuyên nghiệp tại BDS Rental.',
    propertiesCount: a._count.properties
  }))

  return (
    <div className="bg-slate-50 min-h-screen pt-28 pb-16 text-slate-800 text-left">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {/* Breadcrumbs */}
        <nav className="flex text-xs font-semibold text-slate-500 mb-6 space-x-2">
          <Link href="/" className="hover:text-primary transition">Trang chủ</Link>
          <span>/</span>
          <span className="text-slate-850">Danh sách Môi giới / Chủ nhà</span>
        </nav>

        {/* Page Title */}
        <div className="mb-10">
          <span className="text-xs font-bold text-primary tracking-widest uppercase mb-2 block">Professional Agents</span>
          <h1 className="text-3xl font-extrabold text-slate-900 leading-tight">Danh sách Chủ nhà & Môi giới uy tín</h1>
        </div>

        {/* Agents Grid */}
        <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
          {agentsList.length > 0 ? (
            agentsList.map(agent => (
              <div key={agent.id} className="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm hover:shadow-md transition text-center flex flex-col justify-between space-y-4">
                <div className="space-y-3">
                  {/* Avatar circle */}
                  <div className="w-20 h-20 rounded-full bg-slate-50 border border-slate-200 overflow-hidden mx-auto flex items-center justify-center">
                    {agent.avatar ? (
                      <img src={agent.avatar} className="w-full h-full object-cover" alt={agent.name} />
                    ) : (
                      <i className="fa-regular fa-user text-slate-300 text-3xl" />
                    )}
                  </div>

                  <div>
                    <strong className="block text-slate-800 font-bold text-sm truncate">{agent.name}</strong>
                    <span className="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mt-0.5">Chủ nhà / Môi giới</span>
                  </div>

                  <p className="text-[11px] text-slate-500 font-medium line-clamp-2 leading-relaxed h-8">
                    {agent.intro}
                  </p>
                </div>

                <div className="pt-4 border-t border-slate-100 flex flex-col space-y-2.5">
                  <div className="flex justify-between text-[11px] font-bold">
                    <span className="text-slate-400">Tin rao:</span>
                    <span className="text-primary">{agent.propertiesCount} tin đang hoạt động</span>
                  </div>
                  <Link
                    href={`/agents/${agent.id}`}
                    className="w-full py-2 bg-slate-100 hover:bg-primary hover:text-white rounded-xl text-[10px] font-black transition cursor-pointer block"
                  >
                    Xem hồ sơ chi tiết
                  </Link>
                </div>
              </div>
            ))
          ) : (
            <div className="col-span-4 py-16 text-center text-slate-400 text-sm font-semibold bg-white rounded-3xl border border-slate-100 shadow-inner p-8">
              Chưa có môi giới / chủ nhà nào tham gia.
            </div>
          )}
        </div>

      </div>
    </div>
  )
}
