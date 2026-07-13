import { auth } from '@/lib/auth'
import { NextResponse } from 'next/server'

export default auth((req) => {
  const { pathname } = req.nextUrl
  const user = req.auth?.user

  // Protected routes
  if (pathname.startsWith('/profile') && !user) {
    return NextResponse.redirect(new URL('/login', req.url))
  }
  if (pathname.startsWith('/owner') && user?.role !== 'owner') {
    return NextResponse.redirect(new URL('/', req.url))
  }
  if (pathname.startsWith('/admin') && user?.role !== 'admin') {
    return NextResponse.redirect(new URL('/', req.url))
  }
  if ((pathname === '/login' || pathname === '/register') && user) {
    return NextResponse.redirect(new URL('/profile', req.url))
  }
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
