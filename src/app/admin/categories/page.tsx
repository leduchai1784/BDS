import { prisma } from '@/lib/prisma'
import CategoriesTable from '@/components/admin/CategoriesTable'

export const dynamic = 'force-dynamic'

export default async function AdminCategoriesPage() {
  const dbCategories = await prisma.category.findMany({
    include: {
      _count: {
        select: {
          properties: true
        }
      }
    },
    orderBy: {
      createdAt: 'desc'
    }
  })

  const categoriesList = dbCategories.map(c => ({
    id: c.id.toString(),
    name: c.name,
    slug: c.slug,
    description: c.description,
    propertiesCount: c._count.properties
  }))

  return (
    <div className="space-y-6">
      
      {/* Title Header */}
      <div className="text-left">
        <h1 className="text-xl font-bold text-slate-800">Quản lý danh mục</h1>
        <p className="text-xs text-slate-400 mt-1 font-semibold">Quản lý các loại hình phân loại nhà đất phục vụ mục đích tìm kiếm lọc tin.</p>
      </div>

      <CategoriesTable initialCategories={categoriesList} />

    </div>
  )
}
