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

export async function PUT(
  req: Request,
  { params }: { params: Promise<{ id: string }> }
) {
  try {
    const session = await auth()
    if (!session?.user?.id || session.user.role !== 'admin') {
      return NextResponse.json({ error: 'Unauthorized' }, { status: 401 })
    }

    const resolvedParams = await params
    const categoryId = BigInt(resolvedParams.id)
    const { name, description } = await req.json()

    if (!name) {
      return NextResponse.json({ error: 'Vui lòng nhập tên danh mục.' }, { status: 400 })
    }

    const category = await prisma.category.findUnique({
      where: { id: categoryId }
    })

    if (!category) {
      return NextResponse.json({ error: 'Danh mục không tồn tại' }, { status: 404 })
    }

    // Check unique except current
    const existing = await prisma.category.findFirst({
      where: {
        name,
        id: { not: categoryId }
      }
    })

    if (existing) {
      return NextResponse.json({ error: 'Tên danh mục này đã tồn tại.' }, { status: 400 })
    }

    const slug = slugify(name)

    const updated = await prisma.category.update({
      where: { id: categoryId },
      data: {
        name,
        slug,
        description: description || null
      }
    })

    return NextResponse.json({
      success: true,
      message: 'Cập nhật danh mục thành công!',
      category: {
        id: updated.id.toString(),
        name: updated.name,
        slug: updated.slug,
        description: updated.description
      }
    })
  } catch (error: any) {
    console.error('Update category API error:', error)
    return NextResponse.json({ error: error.message }, { status: 500 })
  }
}

export async function DELETE(
  req: Request,
  { params }: { params: Promise<{ id: string }> }
) {
  try {
    const session = await auth()
    if (!session?.user?.id || session.user.role !== 'admin') {
      return NextResponse.json({ error: 'Unauthorized' }, { status: 401 })
    }

    const resolvedParams = await params
    const categoryId = BigInt(resolvedParams.id)

    const category = await prisma.category.findUnique({
      where: { id: categoryId }
    })

    if (!category) {
      return NextResponse.json({ error: 'Danh mục không tồn tại' }, { status: 404 })
    }

    await prisma.category.delete({
      where: { id: categoryId }
    })

    return NextResponse.json({
      success: true,
      message: 'Xóa danh mục thành công!'
    })
  } catch (error: any) {
    console.error('Delete category API error:', error)
    return NextResponse.json({ error: error.message }, { status: 500 })
  }
}
