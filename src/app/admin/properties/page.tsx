import { prisma } from '@/lib/prisma'
import PropertiesTable from '@/components/admin/PropertiesTable'

export const dynamic = 'force-dynamic'

interface AdminPropertiesPageProps {
  searchParams: Promise<{
    search?: string
    categoryId?: string
    status?: string
    id?: string
  }>
}

export default async function AdminPropertiesPage({ searchParams }: AdminPropertiesPageProps) {
  const resolvedSearchParams = await searchParams
  const search = resolvedSearchParams.search || ''
  const categoryId = resolvedSearchParams.categoryId || ''
  const status = resolvedSearchParams.status || ''
  const id = resolvedSearchParams.id || ''

  // Build filters
  const where: any = { deletedAt: null }

  if (search) {
    where.OR = [
      { title: { contains: search, mode: 'insensitive' } },
      { address: { contains: search, mode: 'insensitive' } }
    ]
  }

  if (categoryId) {
    where.categoryId = Number(categoryId)
  }

  if (status) {
    where.status = status
  }

  const [dbProperties, dbCategories] = await Promise.all([
    prisma.property.findMany({
      where,
      include: {
        category: true,
        owner: true
      },
      orderBy: { createdAt: 'desc' }
    }),
    prisma.category.findMany()
  ])

  const propertiesList = dbProperties.map(p => ({
    id: p.id,
    title: p.title,
    address: p.address,
    priceLabel: p.priceLabel,
    area: p.area,
    status: p.status,
    createdAt: p.createdAt ? p.createdAt.toISOString() : '',
    owner: p.owner ? {
      name: p.owner.name,
      email: p.owner.email
    } : null,
    category: p.category ? {
      name: p.category.name
    } : null
  }))

  const categoriesList = dbCategories.map(c => ({
    id: c.id.toString(),
    name: c.name
  }))

  return (
    <div className="space-y-6">
      
      {/* Title Header */}
      <div className="text-left">
        <h1 className="text-xl font-bold text-slate-800">Quản lý tin đăng</h1>
        <p className="text-xs text-slate-400 mt-1 font-semibold">Phê duyệt tin đăng mới, ẩn tin vi phạm hoặc xóa tin đăng cũ khỏi hệ thống.</p>
      </div>

      <PropertiesTable 
        initialProperties={propertiesList} 
        categories={categoriesList} 
        searchParams={{ search, categoryId, status, id }} 
      />

    </div>
  )
}
