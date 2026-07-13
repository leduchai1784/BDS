import { NextResponse } from 'next/server'
import { auth } from '@/lib/auth'
import { prisma } from '@/lib/prisma'

function slugify(text: string): string {
  return text
    .toString()
    .toLowerCase()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .replace(/[^a-z0-9 -]/g, '')
    .replace(/\s+/g, '-')
    .replace(/-+/g, '-')
    .trim()
}

export async function POST(req: Request) {
  try {
    const session = await auth()
    if (!session?.user?.id || session.user.role !== 'admin') {
      return NextResponse.json({ error: 'Unauthorized' }, { status: 401 })
    }

    const { name, description } = await req.json()
    if (!name) {
      return NextResponse.json({ error: 'Vui lòng nhập tên danh mục.' }, { status: 400 })
    }

    // Check for unique name
    const existing = await prisma.category.findFirst({
      where: { name }
    })

    if (existing) {
      return NextResponse.json({ error: 'Tên danh mục này đã tồn tại.' }, { status: 400 })
    }

    const slug = slugify(name)

    const category = await prisma.category.create({
      data: {
        name,
        slug,
        description: description || null
      }
    })

    return NextResponse.json({
      success: true,
      message: 'Tạo danh mục mới thành công!',
      category: {
        id: category.id.toString(),
        name: category.name,
        slug: category.slug,
        description: category.description
      }
    })
  } catch (error: any) {
    console.error('Create category API error:', error)
    return NextResponse.json({ error: error.message }, { status: 500 })
  }
}
