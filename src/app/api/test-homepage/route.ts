import { NextResponse } from 'next/server'
import { prisma } from '@/lib/prisma'
import { getNksProperties } from '@/lib/nks'

export const dynamic = 'force-dynamic'

export async function GET() {
  const steps: string[] = []
  try {
    // Step 1: Query DB Properties
    steps.push('Step 1: Querying db properties...')
    const dbProperties = await prisma.property.findMany({
      where: {
        status: 'approved',
        deletedAt: null
      },
      include: {
        propertyImages: {
          where: { isPrimary: true }
        }
      },
      orderBy: {
        createdAt: 'desc'
      }
    })
    steps.push(`Step 1 Success: Fetched ${dbProperties.length} properties`)

    // Step 2: Map DB properties
    steps.push('Step 2: Mapping db properties...')
    const dbList = dbProperties.map(p => ({
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
      imagePath: p.propertyImages?.[0]?.imagePath || null,
      createdAt: p.createdAt ? p.createdAt.toISOString() : null
    }))
    steps.push('Step 2 Success')

    // Step 3: Fetch NKS properties
    steps.push('Step 3: Querying NKS properties...')
    const nksList = await getNksProperties()
    steps.push(`Step 3 Success: Fetched ${nksList.length} NKS properties`)

    // Step 4: Combine
    steps.push('Step 4: Combining lists...')
    const combined = [...dbList, ...nksList]
    steps.push(`Step 4 Success: combined list has ${combined.length} items`)

    // Step 5: Sort & Filter Featured
    steps.push('Step 5: Sorting & filtering featured listings...')
    const featuredListAll = combined
      .slice()
      .sort((a, b) => {
        if (a.isVip && !b.isVip) return -1
        if (!a.isVip && b.isVip) return 1
        if (a.isNew && !b.isNew) return -1
        if (!a.isNew && b.isNew) return 1
        const dateA = a.createdAt ? new Date(a.createdAt).getTime() : 0
        const dateB = b.createdAt ? new Date(b.createdAt).getTime() : 0
        return dateB - dateA
      })
    steps.push('Step 5 Success')

    // Step 6: Query projects
    steps.push('Step 6: Querying projects...')
    const projects = await prisma.project.findMany({
      take: 6,
      orderBy: {
        createdAt: 'desc'
      }
    })
    steps.push(`Step 6 Success: Fetched ${projects.length} projects`)

    // Step 7: Map projects
    steps.push('Step 7: Mapping projects...')
    const projectList = projects.map(p => ({
      id: Number(p.id),
      title: p.title,
      slug: p.slug,
      description: p.description,
      location: p.location,
      priceRange: p.priceRange,
      status: p.status,
      images: p.images
    }))
    steps.push('Step 7 Success')

    return NextResponse.json({
      success: true,
      message: 'All steps executed successfully',
      steps,
      dbListLength: dbList.length,
      nksListLength: nksList.length,
      projectListLength: projectList.length
    })
  } catch (err: any) {
    return NextResponse.json({
      success: false,
      message: 'Failed during execution',
      error: err.message,
      stack: err.stack,
      steps
    })
  }
}
