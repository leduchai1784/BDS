import NextAuth from 'next-auth'
import { authConfig } from '@/lib/auth.config'
import { NextResponse } from 'next/server'

const { auth } = NextAuth(authConfig)

export default auth((req) => {
  const themeCookie = req.cookies.get('theme')?.value
  const res = NextResponse.next()

  // Ensure theme cookie default fallback if missing
  if (!themeCookie) {
    res.cookies.set('theme', 'light', { path: '/', maxAge: 31536000, sameSite: 'lax' })
  }

  return res
})

export const config = { 
  matcher: [
    '/profile/:path*', 
    '/owner/:path*', 
    '/admin/:path*', 
    '/login', 
    '/register'
  ] 
}
