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

export default async function AdminUsersPage({ searchParams }: AdminUsersPageProps) {
  const session = await auth()
  const resolvedSearchParams = await searchParams
  const search = resolvedSearchParams.search || ''
  const role = resolvedSearchParams.role || ''
  const status = resolvedSearchParams.status || ''

  const currentUserId = session?.user?.id || ''

  // Build prisma query filters
  const where: any = {}

  if (search) {
    where.OR = [
      { name: { contains: search, mode: 'insensitive' } },
      { email: { contains: search, mode: 'insensitive' } },
      { phone: { contains: search, mode: 'insensitive' } }
    ]
  }

  if (role) {
    where.role = role
  }

  if (status) {
    where.status = status
  }

  const dbUsers = await prisma.user.findMany({
    where,
    orderBy: { createdAt: 'desc' }
  })

  const usersList = dbUsers.map(u => ({
    id: u.id.toString(),
    name: u.name,
    email: u.email,
    phone: u.phone,
    avatar: u.avatar,
    role: u.role,
    status: u.status,
    createdAt: u.createdAt ? u.createdAt.toISOString() : ''
  }))

  return (
    <div className="space-y-6">
      
      {/* Title Header */}
      <div className="text-left">
        <h1 className="text-xl font-bold text-slate-800">Quản lý thành viên</h1>
        <p className="text-xs text-slate-400 mt-1 font-semibold">Quản lý tài khoản, thay đổi quyền hạn và khóa/mở khóa các thành viên hệ thống.</p>
      </div>

      {/* Interactive table */}
      <UsersTable 
        initialUsers={usersList} 
        currentUserId={currentUserId} 
        searchParams={{ search, role, status }} 
      />

    </div>
  )
}
