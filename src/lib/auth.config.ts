import type { NextAuthConfig } from 'next-auth'

export const authConfig = {
  pages: {
    signIn: '/login',
    error: '/login'
  },
  session: {
    strategy: 'jwt',
    maxAge: 3 * 24 * 60 * 60 // 3 ngày
  },
  callbacks: {
    authorized({ auth, request: { nextUrl } }) {
      const isLoggedIn = !!auth?.user
      const user = auth?.user as any
      const { pathname } = nextUrl

      // Profile routes: require login
      if (pathname.startsWith('/profile')) {
        return isLoggedIn
      }

      // Owner routes: require owner, agent, or admin role
      if (pathname.startsWith('/owner')) {
        if (isLoggedIn && ['owner', 'agent', 'admin'].includes(user?.role)) return true
        return false
      }

      // Admin routes: require admin role
      if (pathname.startsWith('/admin')) {
        if (isLoggedIn && user?.role === 'admin') return true
        return false
      }

      // Auth routes (login/register): redirect to profile if already logged in
      if (pathname === '/login' || pathname === '/register') {
        if (isLoggedIn) {
          return Response.redirect(new URL('/profile', nextUrl))
        }
        return true
      }

      return true
    },
    async jwt({ token, user }) {
      if (user) {
        token.role = (user as any).role
        token.id = user.id
      }
      return token
    },
    async session({ session, token }) {
      if (session.user) {
        (session.user as any).role = token.role as string
        session.user.id = token.id as string
      }
      return session
    }
  },
  providers: []
} satisfies NextAuthConfig
