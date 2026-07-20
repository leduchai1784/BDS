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

async function fetchNksProperties(): Promise<any[]> {
  try {
    process.env.NODE_TLS_REJECT_UNAUTHORIZED = '0'
    const response = await fetch('https://online.nks.vn/api/nks/rsitems', {
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
        return data.data.map((item: any) => ({
          id: `nks-${item.id}`,
          title: item.title,
          address: item.address || 'Đồng Nai',
          priceLabel: item.formatedPrice || `${(item.price / 1000000000).toFixed(1)} tỷ`,
          area: item.total_area || 0,
          status: 'approved',
          createdAt: new Date().toISOString(),
          owner: item.sale ? {
            name: item.sale.name,
            email: item.sale.email || 'broker@nks.vn'
          } : {
            name: 'Môi giới NKS',
            email: 'broker@nks.vn'
          },
          category: {
            name: item.rstype || 'Bất động sản'
          },
          isNks: true
        }))
      }
    }
    return []
  } catch (err) {
    console.error('Failed to fetch NKS properties:', err)
    return []
  }
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
    } : null,
    isNks: false,
    featureimg: p.images ? (p.images.startsWith('[') ? JSON.parse(p.images)[0] : p.images) : ''
  }))

  const categoriesList = dbCategories.map(c => ({
    id: c.id.toString(),
    name: c.name
  }))

  // Fetch external NKS properties
  let nksProperties: any[] = []
  if (status === '' || status === 'approved') {
    nksProperties = await fetchNksProperties()
    // Filter by search parameters if present
    if (search) {
      const lowSearch = search.toLowerCase()
      nksProperties = nksProperties.filter(item => 
        item.title.toLowerCase().includes(lowSearch) ||
        item.address.toLowerCase().includes(lowSearch)
      )
    }
  }

  const combinedProperties = [...propertiesList, ...nksProperties]

  return (
    <div className="space-y-6">
      
      {/* Title Header */}
      <div className="text-left">
        <h1 className="text-xl font-bold text-slate-800 dark:text-white">Quản lý tin đăng</h1>
        <p className="text-xs text-slate-400 dark:text-slate-400 mt-1 font-semibold">Phê duyệt tin đăng mới, ẩn tin vi phạm, và hiển thị các tin đăng BDS liên kết trực tiếp từ NKS Portal.</p>
      </div>

      <PropertiesTable 
        initialProperties={combinedProperties} 
        categories={categoriesList} 
        searchParams={{ search, categoryId, status, id }} 
      />

    </div>
  )
}
