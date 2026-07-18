const dotenv = require('dotenv')
const path = require('path')
const { PrismaClient } = require('@prisma/client')
const bcrypt = require('bcryptjs')
const axios = require('axios')

dotenv.config({ path: path.join(__dirname, '../.env') })
const prisma = new PrismaClient()

// Copy mapNksUserToLocal and getNksUserInfo from codebase logic
function mapNksUserToLocal(nksUser, token) {
  const sanitize = (str) => str ? String(str).trim() : null
  return {
    nksUserId: String(nksUser.id),
    nksToken: token,
    name: nksUser.name || 'Thành viên NKS',
    phone: sanitize(nksUser.phone),
    avatar: nksUser.avatar || null,
    firstname: sanitize(nksUser.firstname),
    lastname: sanitize(nksUser.lastname),
    dob: sanitize(nksUser.formatedDob || nksUser.dob),
    pob: sanitize(nksUser.pob),
    idNumber: sanitize(nksUser.id_number),
    idDate: sanitize(nksUser.formatedCccdDate || nksUser.id_date),
    idPlace: sanitize(nksUser.id_place),
    cccdFront: nksUser.cccd_front || null,
    cccdBack: nksUser.cccd_back || null,
    addStreet: sanitize(nksUser.add_street),
    addWard: nksUser.add_ward ? String(nksUser.add_ward) : null,
    addDistrict: nksUser.add_district ? String(nksUser.add_district) : null,
    addProvince: nksUser.add_province ? String(nksUser.add_province) : null,
    zaloId: sanitize(nksUser.zalo_id),
    zaloKey: sanitize(nksUser.zalo_key),
    intro: nksUser.intro || null,
    website: sanitize(nksUser.website),
    permanentAddress: sanitize(nksUser.permanent_address || nksUser.pob),
    company: sanitize(nksUser.company),
    province: sanitize(nksUser.province),
    district: sanitize(nksUser.district),
    ward: sanitize(nksUser.ward)
  }
}

async function testAuthorize() {
  const email = 'lehai17082004@gmail.com'
  const password = '12345678'
  process.env.NODE_TLS_REJECT_UNAUTHORIZED = '0'

  try {
    console.log('1. Trying loginNks...')
    const baseUrl = process.env.NKS_AUTH_BASE_URL || 'https://account.nks.vn/api/nks/user'
    const loginRes = await axios.post(`${baseUrl}/login`, {
      username: email,
      password: password,
    }, { timeout: 10000 })

    const json = loginRes.data
    console.log('NKS login raw data keys:', Object.keys(json))
    console.log('NKS login success status:', json.success)

    if (json && json.success && json.data?.access_token) {
      const token = json.data.access_token
      const nksUser = json.data.user
      
      console.log('2. Trying getNksUserInfo...')
      const userRes = await axios.post(baseUrl, { access_token: token }, { timeout: 10000 })
      const userJson = userRes.data
      console.log('getNksUserInfo success:', userJson.success)

      const fullNksUser = userJson.success && userJson.data 
        ? { ...nksUser, ...userJson.data }
        : nksUser

      console.log('3. Mapping fields to local user format...')
      const mappedData = mapNksUserToLocal(fullNksUser, token)
      console.log('Mapped data structure sample:', {
        name: mappedData.name,
        nksUserId: mappedData.nksUserId,
        email: email,
        addProvince: mappedData.addProvince
      })

      console.log('4. Performing Database upsert on Prisma...')
      let localUser = await prisma.user.findUnique({ where: { email } })

      if (localUser) {
        console.log('Local user exists, running update...')
        localUser = await prisma.user.update({
          where: { email },
          data: {
            ...mappedData,
            password: await bcrypt.hash(password, 12),
          }
        })
      } else {
        console.log('Local user does not exist, running create...')
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

      console.log('\n✅ Database operations success!')
      console.log('Created/Updated Local User info:')
      console.log('  ID:', localUser.id.toString())
      console.log('  Email:', localUser.email)
      console.log('  Name:', localUser.name)
    } else {
      console.log('NKS API rejected login token creation.')
    }
  } catch (err) {
    console.error('\n❌ ERROR during authorization flow:', err.message)
    if (err.response) {
      console.error('Response data:', err.response.data)
    }
    if (err.stack) {
      console.error(err.stack)
    }
  } finally {
    await prisma.$disconnect()
  }
}

testAuthorize()
