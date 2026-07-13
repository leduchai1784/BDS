import NextAuth, { DefaultSession } from 'next-auth'
import CredentialsProvider from 'next-auth/providers/credentials'
import { prisma } from './prisma'
import { loginNks, getNksUserInfo, mapNksUserToLocal } from './nks'
import bcrypt from 'bcryptjs'

declare module 'next-auth' {
  interface Session {
    user: {
      id: string
      role: string
    } & DefaultSession['user']
  }
  interface User {
    role?: string
  }
}

export const { handlers, auth, signIn, signOut } = NextAuth({
  providers: [
    CredentialsProvider({
      name: 'credentials',
      credentials: {
        email: { label: 'Email', type: 'text' },
        password: { label: 'Password', type: 'password' }
      },
      async authorize(credentials) {
        if (!credentials?.email || !credentials?.password) {
          throw new Error('MISSING_CREDENTIALS')
        }

        const email = credentials.email as string
        const password = credentials.password as string

        // ┌── BƯỚC 1: Thử NKS Auth API
        const nksLogin = await loginNks(email, password)
        if (nksLogin.success && nksLogin.token) {
          // Lấy profile chi tiết từ NKS
          const nksUserInfo = await getNksUserInfo(nksLogin.token)
          const fullNksUser = nksUserInfo.success && nksUserInfo.data 
            ? { ...nksLogin.user, ...nksUserInfo.data }
            : nksLogin.user

          const mappedData = mapNksUserToLocal(fullNksUser, nksLogin.token)

          // Tìm user cục bộ bằng email
          let localUser = await prisma.user.findUnique({ where: { email } })

          if (localUser) {
            // Cập nhật thông tin từ NKS (giữ nguyên role và các trường cục bộ quan trọng)
            localUser = await prisma.user.update({
              where: { email },
              data: {
                ...mappedData,
                password: await bcrypt.hash(password, 12), // Cập nhật hash pass local
              }
            })
          } else {
            // Tạo mới user local
            localUser = await prisma.user.create({
              data: {
                ...mappedData,
                email,
                role: 'tenant',
                status: 'active',
                password: await bcrypt.hash(password, 12),
              }
            })
          }

          if (localUser.status === 'locked') {
            throw new Error('ACCOUNT_LOCKED')
          }

          return {
            id: String(localUser.id),
            email: localUser.email,
            name: localUser.name,
            role: localUser.role,
            image: localUser.avatar
          }
        }

        // ┌── BƯỚC 2: Fallback Đăng nhập Local (cho Admin hoặc offline fallback)
        const localUser = await prisma.user.findUnique({ where: { email } })
        if (!localUser) {
          throw new Error('INVALID_CREDENTIALS')
        }

        const isValidPassword = await bcrypt.compare(password, localUser.password)
        if (!isValidPassword) {
          throw new Error('INVALID_CREDENTIALS')
        }

        if (localUser.status === 'locked') {
          throw new Error('ACCOUNT_LOCKED')
        }

        return {
          id: String(localUser.id),
          email: localUser.email,
          name: localUser.name,
          role: localUser.role,
          image: localUser.avatar
        }
      }
    })
  ],
  callbacks: {
    async jwt({ token, user }) {
      if (user) {
        token.role = user.role
        token.id = user.id
      }
      return token
    },
    async session({ session, token }) {
      if (session.user) {
        session.user.role = token.role as string
        session.user.id = token.id as string
      }
      return session
    }
  },
  pages: {
    signIn: '/login',
    error: '/login'
  },
  session: {
    strategy: 'jwt'
  }
})
