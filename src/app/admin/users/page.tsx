import { prisma } from '@/lib/prisma'
import { auth } from '@/lib/auth'
import UsersTable from '@/components/admin/UsersTable'

export const dynamic = 'force-dynamic'

interface AdminUsersPageProps {
  searchParams: Promise<{
    search?: string
    role?: string
    status?: string
  }>
}

async function fetchNksAgents(): Promise<any[]> {
  try {
    process.env.NODE_TLS_REJECT_UNAUTHORIZED = '0'
    const response = await fetch('https://online.nks.vn/api/nks/rsagents', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({}),
      next: { revalidate: 30 } // Cache 30 seconds
    })
    if (response.ok) {
      const data = await response.json()
      if (data?.success && Array.isArray(data.data)) {
        return data.data.map((agent: any) => ({
          id: `nks-${agent.id}`,
          name: agent.name,
          email: agent.email || 'nks-broker@nks.vn',
          phone: agent.phone,
          avatar: agent.avatar,
          role: 'agent', // Role môi giới từ NKS
          status: 'active',
          createdAt: new Date().toISOString(),
          isNks: true
        }))
      }
    }
    return []
  } catch (err) {
    console.error('Failed to fetch Nks Agents:', err)
    return []
  }
}

export default async function AdminUsersPage({ searchParams }: AdminUsersPageProps) {
  const session = await auth()
  const resolvedSearchParams = await searchParams
  const search = resolvedSearchParams.search || ''
  const role = resolvedSearchParams.role || ''
  const status = resolvedSearchParams.status || ''

  const currentUserId = session?.user?.id || ''

  // Build prisma query filters for local users
  const where: any = {}

  if (search) {
    where.OR = [
      { name: { contains: search, mode: 'insensitive' } },
      { email: { contains: search, mode: 'insensitive' } },
      { phone: { contains: search, mode: 'insensitive' } }
    ]
  }

  if (role && role !== 'agent') {
    where.role = role
  }

  if (status) {
    where.status = status
  }

  // Fetch local database users
  const dbUsers = await prisma.user.findMany({
    where,
    orderBy: { createdAt: 'desc' }
  })

  let localUsersList = dbUsers.map(u => ({
    id: u.id.toString(),
    name: u.name,
    email: u.email,
    phone: u.phone,
    avatar: u.avatar,
    role: u.role,
    status: u.status,
    createdAt: u.createdAt ? u.createdAt.toISOString() : '',
    isNks: false
  }))

  // Fetch external NKS brokers
  let nksAgents: any[] = []
  if (!role || role === 'agent') {
    nksAgents = await fetchNksAgents()
    // Apply local search filtering on NKS agents if search exists
    if (search) {
      const lowSearch = search.toLowerCase()
      nksAgents = nksAgents.filter(agent => 
        agent.name.toLowerCase().includes(lowSearch) ||
        agent.email.toLowerCase().includes(lowSearch) ||
        (agent.phone && agent.phone.includes(lowSearch))
      )
    }
  }

  // Combine both arrays
  const combinedUsers = [...localUsersList, ...nksAgents]

  return (
    <div className="space-y-6">
      
      {/* Title Header */}
      <div className="text-left">
        <h1 className="text-xl font-bold text-slate-800 dark:text-white">Quản lý thành viên</h1>
        <p className="text-xs text-slate-400 dark:text-slate-400 mt-1 font-semibold">Quản lý tài khoản người dùng hệ thống và danh sách Môi giới lấy trực tiếp từ NKS Portal.</p>
      </div>

      {/* Interactive table */}
      <UsersTable 
        initialUsers={combinedUsers} 
        currentUserId={currentUserId} 
        searchParams={{ search, role, status }} 
      />

    </div>
  )
}
