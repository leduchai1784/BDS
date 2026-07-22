import axios from 'axios'
import { format, parse } from 'date-fns'
import https from 'https'

const httpsAgent = new https.Agent({
  rejectUnauthorized: false
})

const BASE_URL = process.env.NKS_AUTH_BASE_URL || 'https://account.nks.vn/api/nks/user'

export interface NksLoginResult {
  success: boolean
  token?: string
  user?: any
  message: string
}

export interface NksResult {
  success: boolean
  message: string
  data?: any
}



// Map common long Vietnamese ID places to standard short forms
function sanitizeNksString(str: string | null | undefined, maxLength = 50): string {
  if (!str) return ''
  let cleaned = str.trim()
  
  if (maxLength === 50) {
    const lower = cleaned.toLowerCase()
    if (lower.includes('cục cảnh sát quản lý hành chính về trật tự xã hội') || 
        lower.includes('cục cảnh sát qlhc về ttxh')) {
      return 'Cục Cảnh sát QLHC về TTXH'
    }
    if (lower.includes('cục cảnh sát đăng ký quản lý cư trú và dữ liệu quốc gia về dân cư') || 
        lower.includes('cục cảnh sát đkql cư trú và dlqg về dân cư')) {
      return 'Cục Cảnh sát ĐKQL cư trú & DLQG dân cư'
    }
  }
  
  if (cleaned.length > maxLength) {
    return cleaned.substring(0, maxLength)
  }
  return cleaned
}

/**
 * Đăng nhập bằng tài khoản NKS.
 */
export async function loginNks(email: string, password: string): Promise<NksLoginResult> {
  try {
    const response = await axios.post(`${BASE_URL}/login`, {
      username: email,
      password: password,
    }, { timeout: 10000, httpsAgent })

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
  } catch (error: any) {
    console.error('NKS login failed:', error.message)
    return {
      success: false,
      message: 'Không thể kết nối đến máy chủ NKS. Vui lòng thử lại sau.',
    }
  }
}

/**
 * Lấy thông tin chi tiết người dùng từ NKS.
 */
export async function getNksUserInfo(token: string): Promise<NksResult> {
  try {
    const response = await axios.post(BASE_URL, {
      access_token: token
    }, { timeout: 10000, httpsAgent })

    const json = response.data
    if (json && json.success && json.data) {
      return {
        success: true,
        message: json.message || '',
        data: json.data
      }
    }
    return { success: false, message: json?.message || 'Không lấy được thông tin NKS.' }
  } catch (error: any) {
    console.error('NKS getUserInfo failed:', error.message)
    return { success: false, message: error.message }
  }
}

/**
 * Map NKS user array sang data để tạo/cập nhật User local.
 */
export function mapNksUserToLocal(nksUser: any, token: string): any {
  let name = `${nksUser.firstname || ''} ${nksUser.lastname || ''}`.trim()
  if (!name) {
    name = nksUser.name || 'NKS User'
  }

  let dob = nksUser.dob || null
  if (dob && /^\d{4}-\d{2}-\d{2}$/.test(dob)) {
    try {
      dob = format(parse(dob, 'yyyy-MM-dd', new Date()), 'dd/MM/yyyy')
    } catch (e) {}
  }

  let idDate = nksUser.id_date || null
  if (idDate && /^\d{4}-\d{2}-\d{2}$/.test(idDate)) {
    try {
      idDate = format(parse(idDate, 'yyyy-MM-dd', new Date()), 'dd/MM/yyyy')
    } catch (e) {}
  }

  return {
    name,
    firstname: nksUser.firstname || null,
    lastname: nksUser.lastname || null,
    phone: nksUser.phone || null,
    avatar: nksUser.avatar || null,
    nksUserId: String(nksUser.id || ''),
    nksToken: token,
    gender: Number(nksUser.gender || 0),
    dob,
    pob: nksUser.pob || null,
    idNumber: nksUser.id_number || null,
    idDate,
    idPlace: sanitizeNksString(nksUser.id_place, 100) || null,
    cccdFront: nksUser.cccd_front || null,
    cccdBack: nksUser.cccd_back || null,
    addStreet: sanitizeNksString(nksUser.add_street, 250) || null,
    addWard: nksUser.add_ward !== undefined && nksUser.add_ward !== null ? String(nksUser.add_ward) : null,
    addDistrict: nksUser.add_district !== undefined && nksUser.add_district !== null ? String(nksUser.add_district) : null,
    addProvince: nksUser.add_province !== undefined && nksUser.add_province !== null ? String(nksUser.add_province) : null,
    province: nksUser.province || null,
    district: nksUser.district || null,
    ward: nksUser.ward || null,
    permanentAddress: sanitizeNksString(nksUser.permanent_address, 250) || null,
    zaloId: nksUser.zalo_id || null,
    zaloKey: nksUser.zalo_key || null,
    intro: nksUser.intro || null,
    website: nksUser.website || null,
    rslogan: nksUser.rslogan || null,
    rsbio: nksUser.rsbio || null,
    rsexperience: nksUser.rsexperience || null,
    rslocation: nksUser.rslocation || null,
    rsachievement: nksUser.rsachievement || null,
    rscertificate: nksUser.rscertificate || null,
  }
}

/**
 * Map local User model fields back to NKS API keys.
 */
export function mapLocalUserToNks(user: any): any {
  let dob = user.dob
  if (dob && /^\d{2}\/\d{2}\/\d{4}$/.test(dob)) {
    try {
      dob = format(parse(dob, 'dd/MM/yyyy', new Date()), 'yyyy-MM-dd')
    } catch (e) {}
  }

  let idDate = user.idDate
  if (idDate && /^\d{2}\/\d{2}\/\d{4}$/.test(idDate)) {
    try {
      idDate = format(parse(idDate, 'dd/MM/yyyy', new Date()), 'yyyy-MM-dd')
    } catch (e) {}
  }

  return {
    name: user.name,
    firstname: user.firstname,
    lastname: user.lastname,
    phone: user.phone,
    email: user.email,
    gender: user.gender,
    dob,
    pob: user.pob,
    id_number: user.idNumber,
    id_date: idDate,
    id_place: user.idPlace,
    permanent_address: user.permanentAddress,
    add_street: user.addStreet,
    add_ward: user.addWard,
    add_district: user.addDistrict,
    add_province: user.addProvince,
    zalo_id: user.zaloId,
    zalo_key: user.zaloKey,
    intro: user.intro,
    website: user.website,
  }
}

/**
 * Cập nhật thông tin cá nhân lên NKS.
 */
export async function updateNksInfo(token: string, localUser: any, updateData: any): Promise<NksResult> {
  try {
    const updatableKeys = [
      'name', 'firstname', 'lastname', 'phone', 'email', 'gender', 'dob', 'pob',
      'id_number', 'id_date', 'id_place', 'permanent_address',
      'add_street', 'add_ward', 'add_district', 'add_province',
      'intro', 'website', 'zalo_id', 'zalo_key', 'role_id'
    ]

    const mergedData: any = {}
    
    // Map local user fields to NKS format first
    const mappedLocal = mapLocalUserToNks(localUser)
    for (const key of updatableKeys) {
      if (mappedLocal[key] !== undefined && mappedLocal[key] !== null) {
        mergedData[key] = mappedLocal[key]
      }
    }

    // Overwrite with update data (remap standard fields)
    for (const [key, val] of Object.entries(updateData)) {
      if (updatableKeys.includes(key)) {
        mergedData[key] = val
      }
    }

    // NKS expects Ward, District, Province as Ward ID integers
    // Remap add_* keys to short forms Ward/District/Province for NKS
    const remap = { province: 'add_province', ward: 'add_ward', district: 'add_district' }
    for (const [nksKey, ourKey] of Object.entries(remap)) {
      if (mergedData[ourKey] !== undefined && !isNaN(Number(mergedData[ourKey]))) {
        mergedData[nksKey] = Number(mergedData[ourKey])
      }
      delete mergedData[ourKey]
    }

    if (mergedData.id_place) {
      mergedData.id_place = sanitizeNksString(mergedData.id_place, 50)
    }
    if (mergedData.pob) {
      mergedData.pob = sanitizeNksString(mergedData.pob, 100)
    }

    // Remap date fields if they are in updateData in dd/MM/yyyy format
    if (updateData.dob && /^\d{2}\/\d{2}\/\d{4}$/.test(updateData.dob)) {
      try {
        mergedData.dob = format(parse(updateData.dob, 'dd/MM/yyyy', new Date()), 'yyyy-MM-dd')
      } catch (e) {}
    }
    if (updateData.id_date && /^\d{2}\/\d{2}\/\d{4}$/.test(updateData.id_date)) {
      try {
        mergedData.id_date = format(parse(updateData.id_date, 'dd/MM/yyyy', new Date()), 'yyyy-MM-dd')
      } catch (e) {}
    }

    const payload = { ...mergedData, access_token: token }

    const response = await axios.post(`${BASE_URL}/updateInfo`, payload, { timeout: 10000, httpsAgent })
    const json = response.data

    return {
      success: !!(response.status === 200 && json?.success),
      message: json?.message || '',
    }
  } catch (error: any) {
    console.error('NKS updateInfo failed:', error.message)
    return { success: false, message: error.message }
  }
}

/**
 * Cập nhật avatar lên NKS.
 */
export async function updateNksAvatar(token: string, base64Data: string): Promise<NksResult> {
  try {
    const response = await axios.post(`${BASE_URL}/updateAvatar`, {
      avatar: base64Data,
      access_token: token
    }, { timeout: 15000, httpsAgent })

    const json = response.data
    return {
      success: !!(response.status === 200 && json?.success),
      message: json?.message || '',
      data: json?.data
    }
  } catch (error: any) {
    console.error('NKS updateAvatar failed:', error.message)
    return { success: false, message: error.message }
  }
}

/**
 * Cập nhật CCCD lên NKS.
 */
export async function updateNksCccd(token: string, data: any): Promise<NksResult> {
  try {
    const idPlace = sanitizeNksString(data.id_place || data.place || '', 50)
    const response = await axios.post(`${BASE_URL}/updateCccd`, {
      front: data.cccd_front || '',
      back: data.cccd_back || '',
      number: data.id_number || '',
      date: data.id_date || '',
      place: idPlace,
      access_token: token
    }, { timeout: 20000, httpsAgent })

    const json = response.data
    return {
      success: !!(response.status === 200 && json?.success),
      message: json?.message || '',
      data: json?.data || {}
    }
  } catch (error: any) {
    console.error('NKS updateCccd failed:', error.message)
    return { success: false, message: error.message }
  }
}

/**
 * Cập nhật mật khẩu lên NKS.
 */
export async function updateNksPassword(token: string, oldPassword: string, newPassword: string): Promise<NksResult> {
  try {
    const response = await axios.post(`${BASE_URL}/updatePass`, {
      old_password: oldPassword,
      password: newPassword,
      access_token: token
    }, { timeout: 10000, httpsAgent })

    const json = response.data
    return {
      success: !!(response.status === 200 && json?.success),
      message: json?.message || '',
    }
  } catch (error: any) {
    console.error('NKS updatePass failed:', error.message)
    return { success: false, message: error.message }
  }
}

/**
 * Fetch and transform properties from the NKS Online API
 */
export async function getNksProperties(): Promise<any[]> {
  try {
    const response = await axios.post('https://online.nks.vn/api/nks/rsitems', {}, { timeout: 10000 })
    const json = response.data

    if (json && json.success && Array.isArray(json.data)) {
      return json.data.map((item: any) => {
        const id = String(item.id || Math.floor(Math.random() * 100000))
        const title = item.title || 'Bất động sản BDS Rental'
        
        let lat = 10.7822
        let lng = 106.6704
        if (item.geolocation) {
          const coords = item.geolocation.split(',')
          if (coords.length === 2) {
            lat = parseFloat(coords[0].trim()) || 10.7822
            lng = parseFloat(coords[1].trim()) || 106.6704
          }
        }

        const rsType = item.rstype || 'Căn hộ'
        const propertyType = rsType // Keep exact original NKS category string

        const area = Number(item.total_area) || 55
        let price = 0
        if (item.rentprice && Number(item.rentprice) > 0) {
          price = Number(item.rentprice)
        } else if (item.price && Number(item.price) > 0) {
          price = Number(item.price)
        }

        const isRent = (item.rentprice && Number(item.rentprice) > 0) || (item.formatedRentPrice && item.formatedRentPrice.includes('tháng'))

        let priceLabel = 'Thỏa thuận'
        if (item.formatedRentPrice) {
          priceLabel = item.formatedRentPrice.replace('triệu', 'tr').trim() + '/tháng'
        } else if (item.formatedPrice) {
          priceLabel = item.formatedPrice.replace('triệu', 'tr').trim()
        }

        const address = item.address || ''
        const location = address.replace(', Việt Nam', '').replace(', Afghanistan', '').trim()

        let featureImg = item.featureimg || '/images/apartment_placeholder.png'
        if (featureImg.startsWith('//')) {
          featureImg = 'https:' + featureImg
        }

        // Extract images array if available
        let allImages: string[] = []
        if (featureImg) {
          allImages.push(featureImg)
        }
        if (item.images) {
          try {
            const parsedImgs = typeof item.images === 'string' ? JSON.parse(item.images) : item.images
            if (Array.isArray(parsedImgs)) {
              parsedImgs.forEach((img: string) => {
                let fullImgUrl = img
                if (!fullImgUrl.startsWith('http') && !fullImgUrl.startsWith('//')) {
                  fullImgUrl = `https://data.nks.vn/storage/${img.replace(/^\//, '')}`
                } else if (fullImgUrl.startsWith('//')) {
                  fullImgUrl = 'https:' + fullImgUrl
                }
                if (!allImages.includes(fullImgUrl)) {
                  allImages.push(fullImgUrl)
                }
              })
            }
          } catch (e) {}
        }

        return {
          id,
          title,
          price,
          priceLabel,
          sqrPriceLabel: item.formatedSqrPrice ? `${item.formatedSqrPrice}/m²` : '',
          area,
          bedroom: Number(item.bed || item.bedroom) || 0,
          bathroom: Number(item.bath || item.bathroom) || 0,
          direction: item.direction || 'Không xác định',
          floors: Number(item.floors) || 0,
          address: location || item.province || 'Thành phố Hồ Chí Minh',
          district: item.district || 'HCMC',
          city: item.province || 'Thành phố Hồ Chí Minh',
          latitude: lat,
          longitude: lng,
          imagePath: featureImg,
          images: allImages,
          isVip: !!item.is_vip || false,
          isNew: !!item.is_new || false,
          propertyType,
          isRent: !!isRent,
          saleEmail: item.email || item.sale?.email || '',
          salePhone: item.phone || item.sale?.phone || '',
          sale: item.sale ? {
            id: item.sale.id,
            name: item.sale.name || (item.sale.firstname ? `${item.sale.lastname || ''} ${item.sale.firstname}`.trim() : 'Môi giới NKS'),
            phone: item.sale.phone || '',
            email: item.sale.email || '',
            avatar: item.sale.avatar || null
          } : null
        }
      })
    }
    return []
  } catch (error: any) {
    console.warn('Failed to fetch properties from NKS API:', error.message)
    return []
  }
}

import { unstable_cache } from 'next/cache'

/**
 * Fetch provinces from NKS API (Cached for 24 hours)
 */
export const getNksProvinces = unstable_cache(
  async (): Promise<any[]> => {
    try {
      const response = await axios.post('https://online.nks.vn/api/nks/provinces', {}, { timeout: 15000 })
      const json = response.data
      if (json && json.success && Array.isArray(json.data)) {
        return json.data
      }
    } catch (error: any) {
      console.warn('Failed to fetch provinces from NKS API:', error.message)
    }
    return []
  },
  ['nks-provinces'],
  { revalidate: 86400 } // 24 hours
)

/**
 * Fetch wards/administratives for a province from NKS API (Cached for 24 hours)
 */
export const getNksWardsByProvince = unstable_cache(
  async (provinceId: number): Promise<any[]> => {
    try {
      const response = await axios.post('https://online.nks.vn/api/nks/administratives', {
        province_id: provinceId,
        slcBox: true
      }, { timeout: 15000 })
      const json = response.data
      if (json && json.success && Array.isArray(json.data)) {
        return json.data
      }
    } catch (error: any) {
      console.warn(`Failed to fetch wards for province ${provinceId} from NKS API:`, error.message)
    }
    return []
  },
  ['nks-wards-province'],
  { revalidate: 86400 }
)

export async function getNksWards(): Promise<any[]> {
  return getNksWardsByProvince(79)
}

/**
 * 7. Tạo tin đăng (Authentication)
 * URL: https://account.nks.vn/api/nks/user/rsitem/create
 */
export async function createNksProperty(token: string, payload: any): Promise<NksResult> {
  try {
    const response = await axios.post(`${BASE_URL}/rsitem/create`, {
      access_token: token,
      ...payload
    }, { timeout: 15000, httpsAgent })

    const json = response.data
    if (json && json.success) {
      return { success: true, message: json.message || 'Tạo tin đăng NKS thành công.', data: json.data }
    }
    return { success: false, message: json?.message || 'Không thể tạo tin đăng trên NKS.' }
  } catch (error: any) {
    console.error('NKS createProperty failed:', error.message)
    return { success: false, message: error.response?.data?.message || 'Lỗi kết nối NKS API tạo tin.' }
  }
}

/**
 * 8. Cập nhật tin đăng (Authentication)
 * URL: https://account.nks.vn/api/nks/user/rsitem/update
 */
export async function updateNksProperty(token: string, payload: any): Promise<NksResult> {
  try {
    const response = await axios.post(`${BASE_URL}/rsitem/update`, {
      access_token: token,
      ...payload
    }, { timeout: 15000, httpsAgent })

    const json = response.data
    if (json && json.success) {
      return { success: true, message: json.message || 'Cập nhật tin đăng NKS thành công.', data: json.data }
    }
    return { success: false, message: json?.message || 'Không thể cập nhật tin đăng NKS.' }
  } catch (error: any) {
    console.error('NKS updateProperty failed:', error.message)
    return { success: false, message: error.response?.data?.message || 'Lỗi kết nối NKS API cập nhật.' }
  }
}

/**
 * 9. Xóa tin đăng (Authentication)
 * URL: https://account.nks.vn/api/nks/user/rsitem/delete
 */
export async function deleteNksProperty(token: string, propertyId: string | number): Promise<NksResult> {
  try {
    const response = await axios.post(`${BASE_URL}/rsitem/delete`, {
      access_token: token,
      id: propertyId
    }, { timeout: 10000, httpsAgent })

    const json = response.data
    if (json && json.success) {
      return { success: true, message: json.message || 'Rsitem deleted successfully.' }
    }
    return { success: false, message: json?.message || 'Không thể xóa tin đăng trên NKS.' }
  } catch (error: any) {
    console.error('NKS deleteProperty failed:', error.message)
    return { success: false, message: error.response?.data?.message || 'Lỗi kết nối NKS API xóa tin.' }
  }
}
