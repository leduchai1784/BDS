import { clsx, type ClassValue } from 'clsx'
import { twMerge } from 'tailwind-merge'

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs))
}

/**
 * Format raw number/BigInt price into a human-readable Vietnamese format (e.g. 5.2 tỷ, 8.5 triệu)
 */
export function formatPrice(price: number | bigint | string, transactionType: string = 'rent'): string {
  const numPrice = Number(price)
  if (isNaN(numPrice) || numPrice <= 0) return 'Thỏa thuận'

  let formatted = ''
  if (numPrice >= 1000000000) {
    const tyVal = numPrice / 1000000000
    // Round to 2 decimal places max
    formatted = `${parseFloat(tyVal.toFixed(2)).toLocaleString('vi-VN')} tỷ`
  } else if (numPrice >= 1000000) {
    const trieuVal = numPrice / 1000000
    formatted = `${parseFloat(trieuVal.toFixed(2)).toLocaleString('vi-VN')} triệu`
  } else {
    formatted = `${numPrice.toLocaleString('vi-VN')} đ`
  }

  if (transactionType === 'rent') {
    return `${formatted}/tháng`
  }
  return formatted
}

/**
 * Generate a clean, URL-safe slug from a string (Vietnamese supported)
 */
export function generateSlug(str: string): string {
  let slug = str.toLowerCase().trim()

  // Replace Vietnamese characters
  slug = slug.replace(/[áàảãạăắằẳẵặâấầẩẫậ]/g, 'a')
  slug = slug.replace(/[éèẻẽẹêếềểễệ]/g, 'e')
  slug = slug.replace(/[íìỉĩị]/g, 'i')
  slug = slug.replace(/[óòỏõọôốồổỗộơớờởỡợ]/g, 'o')
  slug = slug.replace(/[úùủũụưứừửữự]/g, 'u')
  slug = slug.replace(/[ýỳỷỹỵ]/g, 'y')
  slug = slug.replace(/đ/g, 'd')

  // Remove special characters, replace spaces with hyphens
  slug = slug.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
    .replace(/\s+/g, '-') // collapse whitespace and replace by -
    .replace(/-+/g, '-') // collapse dashes

  // Append a unique suffix to prevent duplicates (mimicking Laravel's uniqid)
  const uniqueSuffix = Math.random().toString(36).substring(2, 7)
  return `${slug}-${uniqueSuffix}`
}
