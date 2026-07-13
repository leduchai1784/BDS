import { NextResponse } from 'next/server'
import { prisma } from '@/lib/prisma'
import { getNksProvinces, getNksWards } from '@/lib/nks'

export async function GET(req: Request) {
  try {
    const { searchParams } = new URL(req.url)
    const q = searchParams.get('q') || ''
    
    if (q.trim().length < 2) {
      return NextResponse.json([])
    }

    const query = q.trim()
    const suggestions: any[] = []

    // 1. Tỉnh/Thành phố & Quận/Huyện từ NKS Provinces API
    const nksProvinces = await getNksProvinces()
    let matchedProvincesCount = 0
    let matchedDistrictsCount = 0
    const seenDistricts: string[] = []

    for (const prov of nksProvinces) {
      const provTitle = prov.title || ''
      
      // Match Province
      if (matchedProvincesCount < 3 && provTitle.toLowerCase().includes(query.toLowerCase())) {
        suggestions.push({
          type: 'city',
          label: provTitle,
          sublabel: 'Tỉnh / Thành phố',
          value: provTitle
        })
        matchedProvincesCount++
      }

      // Match Districts (under administratives)
      if (Array.isArray(prov.administratives)) {
        for (const dist of prov.administratives) {
          const distTitle = dist.title || ''
          if (distTitle.toLowerCase().includes(query.toLowerCase())) {
            const isWard = distTitle.toLowerCase().startsWith('phường ') || 
                           distTitle.toLowerCase().startsWith('xã ') || 
                           distTitle.toLowerCase().startsWith('thị trấn ')
            
            if (isWard) {
              suggestions.push({
                type: 'ward',
                label: distTitle,
                sublabel: `Phường / Xã (${provTitle})`,
                value: distTitle
              })
            } else {
              if (matchedDistrictsCount < 3) {
                const key = `${distTitle}|${provTitle}`
                if (!seenDistricts.includes(key)) {
                  seenDistricts.push(key)
                  suggestions.push({
                    type: 'district',
                    label: distTitle,
                    sublabel: `Quận / Huyện (${provTitle})`,
                    value: distTitle
                  })
                  matchedDistrictsCount++
                }
              }
            }
          }
        }
      }
    }

    // 2. Phường/Xã từ NKS Wards API
    const nksWards = await getNksWards()
    let matchedWardsCount = 0
    const seenWards: string[] = []

    for (const ward of nksWards) {
      const wardTitle = ward.title || ''
      
      // Skip district/province level entries in administratives
      const isHigherLevel = wardTitle.includes('Thị xã') || 
                            wardTitle.includes('Huyện') || 
                            wardTitle.includes('Quận') || 
                            wardTitle.includes('Thành phố')
      if (isHigherLevel) continue

      if (matchedWardsCount < 3 && wardTitle.toLowerCase().includes(query.toLowerCase())) {
        if (!seenWards.includes(wardTitle)) {
          seenWards.push(wardTitle)
          suggestions.push({
            type: 'ward',
            label: wardTitle,
            sublabel: 'Phường / Xã',
            value: wardTitle
          })
          matchedWardsCount++
        }
      }
    }

    // 3. Đường / Địa chỉ cụ thể từ Database
    const addresses = await prisma.property.findMany({
      where: {
        address: { contains: query, mode: 'insensitive' },
        status: 'approved',
        deletedAt: null
      },
      select: { address: true },
      distinct: ['address'],
      take: 3
    })

    for (const addr of addresses) {
      suggestions.push({
        type: 'address',
        label: addr.address,
        sublabel: 'Địa chỉ cụ thể',
        value: addr.address
      })
    }

    // 4. Tên bất động sản (Tiêu đề tin đăng) từ Database
    const titles = await prisma.property.findMany({
      where: {
        title: { contains: query, mode: 'insensitive' },
        status: 'approved',
        deletedAt: null
      },
      select: { id: true, title: true, address: true },
      take: 4
    })

    for (const t of titles) {
      suggestions.push({
        type: 'property',
        label: t.title,
        sublabel: `Bất động sản (${t.address})`,
        value: t.title,
        id: t.id
      })
    }

    return NextResponse.json(suggestions)
  } catch (error: any) {
    console.error('Autocomplete error:', error)
    return NextResponse.json({ error: error.message }, { status: 500 })
  }
}
