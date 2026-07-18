const { PrismaClient } = require('@prisma/client')
const prisma = new PrismaClient()
const bcrypt = require('bcryptjs')
const axios = require('axios')

// Mock loginNks and getNksUserInfo logic
const BASE_URL = 'https://account.nks.vn/api/nks/user'

async function loginNks(email, password) {
  try {
    const response = await axios.post(`${BASE_URL}/login`, {
      username: email,
      password: password,
    }, { timeout: 10000 })

    const json = response.data
    if (json && json.success && json.data?.access_token) {
      return {
        success: true,
        token: json.data.access_token,
        user: json.data.user,
        message: json.message || 'Đăng nhập thành công.',
      }
    }
    return {
      success: false,
      message: json?.message || 'Thông tin đăng nhập NKS không chính xác.',
    }
  } catch (error) {
    return {
      success: false,
      message: error.message
    }
  }
}

async function testAuthorize(email, password) {
  console.log(`\n--- TESTING AUTH FOR: ${email} ---`)

  // Step 1: NKS Auth
  const nksLogin = await loginNks(email, password)
  console.log('NKS Login result:', nksLogin)

  if (nksLogin.success && nksLogin.token) {
    console.log('NKS Authentication succeeded! Simulating database update/create...')
    
    let localUser = await prisma.user.findUnique({ where: { email } })
    console.log('Found local user:', localUser ? { id: localUser.id, email: localUser.email, role: localUser.role } : 'Not found')

    if (localUser) {
      // Check if mappedData has anything that might affect it (actually it does not contain role)
      console.log('Would update local user role to:', localUser.role)
    } else {
      console.log('Would create local user with role: tenant')
    }
    
    return
  }

  // Step 2: Fallback local auth
  console.log('NKS Auth failed. Simulating local auth fallback...')
  const localUser = await prisma.user.findUnique({ where: { email } })
  if (!localUser) {
    console.log('Error: Local user not found.')
    return
  }

  const isValidPassword = await bcrypt.compare(password, localUser.password)
  console.log('Bcrypt password validation:', isValidPassword ? 'VALID' : 'INVALID')
  
  if (isValidPassword) {
    console.log('Local login succeeded! Authenticated user:', {
      id: String(localUser.id),
      email: localUser.email,
      name: localUser.name,
      role: localUser.role
    })
  } else {
    console.log('Local login failed: Invalid password.')
  }
}

async function main() {
  await testAuthorize('admin@nks.com.vn', 'password')
}

main()
  .catch(console.error)
  .finally(() => prisma.$disconnect())
