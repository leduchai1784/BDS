import NextAuth, { DefaultSession } from 'next-auth'
import { authConfig } from './auth.config'
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
  ...authConfig,
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
          const nksUserInfo = await getNksUserInfo(nksLogin.token)
          const fullNksUser = nksUserInfo.success && nksUserInfo.data 
            ? { ...nksLogin.user, ...nksUserInfo.data }
            : nksLogin.user

          const mappedData = mapNksUserToLocal(fullNksUser, nksLogin.token)

          // Nhận biết vai trò (Role) từ NKS API linh hoạt dựa vào role_id, role.id hoặc role.name
          let mappedRole = 'tenant' // Mặc định tài khoản NKS API là Khách thuê (User)
          const roleId = Number(fullNksUser.role_id || fullNksUser.role?.id || 0)
          const nksRoleName = String(fullNksUser.role?.name || fullNksUser.role_name || fullNksUser.role || '').toLowerCase()

          if (roleId === 1 || nksRoleName.includes('admin') || nksRoleName.includes('quản trị')) {
            mappedRole = 'admin'
          } else if (roleId === 3 || nksRoleName.includes('owner') || nksRoleName.includes('chủ nhà')) {
            mappedRole = 'owner'
          } else if (roleId === 4 || nksRoleName.includes('agent') || nksRoleName.includes('broker') || nksRoleName.includes('môi giới') || nksRoleName.includes('sale')) {
            mappedRole = 'agent'
          } else if (roleId === 2 || roleId === 5 || nksRoleName.includes('tenant') || nksRoleName.includes('user') || nksRoleName.includes('khách')) {
            mappedRole = 'tenant'
          } else {
            mappedRole = 'tenant' // Mặc định là tenant cho an toàn bảo mật
          }

          let userId = '100'
          let userRole = mappedRole
          let userName = mappedData.name || fullNksUser.name || 'NKS Agent'
          let userAvatar = mappedData.avatar || fullNksUser.avatar || null

          try {
            let localUser = await prisma.user.findUnique({ where: { email } })

            if (localUser) {
              localUser = await prisma.user.update({
                where: { email },
                data: {
                  ...mappedData,
                  role: localUser.role === 'admin' ? 'admin' : mappedRole,
                  password: await bcrypt.hash(password, 12),
                }
              })
            } else {
              localUser = await prisma.user.create({
                data: {
                  ...mappedData,
                  email,
                  role: mappedRole,
                  status: 'active',
                  password: await bcrypt.hash(password, 12),
                }
              })
            }

            if (localUser.status === 'locked') {
              throw new Error('ACCOUNT_LOCKED')
            }

            userId = String(localUser.id)
            userRole = localUser.role
            userName = localUser.name
            userAvatar = localUser.avatar
          } catch (dbErr: any) {
            if (dbErr.message === 'ACCOUNT_LOCKED') throw dbErr
            console.error('Non-fatal error syncing NKS user to DB:', dbErr.message)
            // Fallback try simple create
            try {
              let localUser = await prisma.user.findUnique({ where: { email } })
              if (!localUser) {
                localUser = await prisma.user.create({
                  data: {
                    email,
                    name: userName,
                    phone: mappedData.phone || null,
                    avatar: userAvatar,
                    role: mappedRole,
                    status: 'active',
                    nksUserId: mappedData.nksUserId || null,
                    nksToken: nksLogin.token,
                    password: await bcrypt.hash(password, 12),
                  }
                })
              }
              userId = String(localUser.id)
              userRole = mappedRole
            } catch (retryErr: any) {
              console.error('DB sync retry failed:', retryErr.message)
            }
          }

          return {
            id: userId,
            email: email,
            name: userName,
            role: userRole,
            image: userAvatar,
            nksToken: nksLogin.token
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
    ...authConfig.callbacks,
    async jwt({ token, user }) {
      if (user) {
        token.role = (user as any).role
        token.id = user.id
        token.avatar = (user as any).image || (user as any).avatar || null
        token.nksToken = (user as any).nksToken
      }
      // Fetch fresh role and avatar from DB on subsequent requests to prevent stale permissions
      else if (token?.id) {
        try {
          const uId = Number(token.id)
          if (!isNaN(uId) && uId > 0) {
            const dbUser = await prisma.user.findUnique({
              where: { id: uId },
              select: { role: true, nksToken: true, avatar: true }
            })
            if (dbUser) {
              token.role = dbUser.role
              token.avatar = dbUser.avatar
              if (dbUser.nksToken) token.nksToken = dbUser.nksToken
            } else {
              // Tài khoản đã bị xóa khỏi DB local -> Hủy Token để xóa Cookie trình duyệt
              return null
            }
          }
        } catch (e) {
          console.error('Failed to fetch fresh user role for token:', e)
        }
      }
      return token
    },
    async session({ session, token }) {
      if (session.user) {
        (session.user as any).role = token.role as string;
        (session.user as any).avatar = token.avatar as string;
        (session.user as any).nksToken = token.nksToken as string;
        session.user.image = token.avatar as string;
        session.user.id = token.id as string;
      }
      return session
    }
  }
})
