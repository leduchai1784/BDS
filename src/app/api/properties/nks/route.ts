import { NextResponse } from 'next/server'
import { auth } from '@/lib/auth'
import { createNksProperty, updateNksProperty, deleteNksProperty } from '@/lib/nks'

export const dynamic = 'force-dynamic'

export async function POST(req: Request) {
  try {
    const session = await auth()
    const user = session?.user as any
    const nksToken = user?.nksToken

    if (!nksToken) {
      return NextResponse.json({
        success: false,
        error: 'Bạn cần đăng nhập tài khoản NKS để thực hiện thao tác này.'
      }, { status: 401 })
    }

    const body = await req.json()
    const { action } = body

    if (action === 'delete') {
      const { id } = body
      if (!id) {
        return NextResponse.json({ success: false, error: 'Thiếu ID tin đăng NKS cần xóa.' }, { status: 400 })
      }
      const res = await deleteNksProperty(nksToken, id)
      return NextResponse.json(res)
    }

    if (action === 'update') {
      const res = await updateNksProperty(nksToken, body.data)
      return NextResponse.json(res)
    }

    // Default action: Create property
    const payload = {
      code: crypto.randomUUID(),
      title: body.title,
      slug: (body.title || '')
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[đĐ]/g, 'd')
        .replace(/[^a-z0-9\s-]/g, '')
        .trim()
        .replace(/\s+/g, '-'),
      featureimg: body.featureimg || '',
      geolocation: body.latitude && body.longitude ? `${body.latitude},${body.longitude}` : '10.7822,106.6704',
      street_number: body.street_number || '',
      street_area: body.street_area || '',
      fulladd: body.address || '',
      phone: body.phone || user?.phone || '',
      email: body.email || user?.email || '',
      price: body.onsale === 1 ? Number(body.price) : null,
      sqrprice: body.onsale === 1 && body.total_area ? Math.round(Number(body.price) / Number(body.total_area)) : null,
      rentprice: body.onsale === 0 ? Number(body.price) : null,
      sqrrentprice: body.onsale === 0 && body.total_area ? Math.round(Number(body.price) / Number(body.total_area)) : null,
      rentdeposit: body.rentdeposit ? Number(body.rentdeposit) : null,
      commision: body.commision ? Number(body.commision) : 0,
      direction: body.direction || 'Bắc',
      total_area: Number(body.total_area || body.area || 50),
      bed: Number(body.bed || body.bedroom || 1),
      bath: Number(body.bath || body.bathroom || 1),
      onsale: body.onsale !== undefined ? Number(body.onsale) : 0, // 0: rent, 1: sale
      description: body.description || body.title,
      rstype: body.rstype || 'Căn hộ', // Keep exact NKS category string
      legal: body.legal || 'Sổ đỏ/Sổ hồng'
    }

    const res = await createNksProperty(nksToken, payload)
    return NextResponse.json(res)
  } catch (error: any) {
    console.error('API /api/properties/nks Error:', error)
    return NextResponse.json({
      success: false,
      error: error.message || 'Lỗi xử lý API NKS Property'
    }, { status: 500 })
  }
}
