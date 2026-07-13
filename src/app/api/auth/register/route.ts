import { NextResponse } from 'next/server'
import { prisma } from '@/lib/prisma'
import bcrypt from 'bcryptjs'
import { z } from 'zod'

const registerSchema = z.object({
  name: z.string().min(1, 'Họ tên không được để trống'),
  email: z.string().email('Email không đúng định dạng'),
  phone: z.string().optional().nullable(),
  role: z.enum(['tenant', 'owner'], { errorMap: () => ({ message: 'Vai trò không hợp lệ' }) }),
  password: z.string().min(8, 'Mật khẩu phải từ 8 ký tự trở lên'),
})

export async function POST(req: Request) {
  try {
    const body = await req.json()
    
    // Validate inputs
    const parsed = registerSchema.safeParse(body)
    if (!parsed.success) {
      return NextResponse.json({
        success: false,
        message: parsed.error.errors[0].message,
        errors: parsed.error.format()
      }, { status: 400 })
    }

    const { name, email, phone, role, password } = parsed.data

    // Check if email already exists
    const existingUser = await prisma.user.findUnique({
      where: { email }
    })

    if (existingUser) {
      return NextResponse.json({
        success: false,
        message: 'Email này đã được sử dụng.'
      }, { status: 400 })
    }

    // Hash password
    const hashedPassword = await bcrypt.hash(password, 12)

    // Create user in DB
    await prisma.user.create({
      data: {
        name,
        email,
        phone: phone || null,
        role,
        status: 'active',
        password: hashedPassword
      }
    })

    return NextResponse.json({
      success: true,
      message: 'Đăng ký tài khoản thành công! Vui lòng đăng nhập.'
    })
  } catch (error: any) {
    console.error('Registration error:', error)
    return NextResponse.json({
      success: false,
      message: 'Có lỗi xảy ra trong quá trình đăng ký. Vui lòng thử lại sau.'
    }, { status: 500 })
  }
}
