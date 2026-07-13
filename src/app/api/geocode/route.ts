import { NextResponse } from 'next/server'
import axios from 'axios'

export async function GET(req: Request) {
  try {
    const { searchParams } = new URL(req.url)
    const q = searchParams.get('q') || ''

    if (!q.trim()) {
      return NextResponse.json([])
    }

    const response = await axios.get('https://nominatim.openstreetmap.org/search', {
      params: {
        format: 'json',
        q: q,
        limit: 1
      },
      headers: {
        'User-Agent': 'BdsRentalApp/1.0 (lehai17082004@gmail.com)'
      },
      timeout: 8000
    })

    const headers = {
      'Cache-Control': 'public, max-age=2592000, s-maxage=2592000, stale-while-revalidate=86400'
    }

    return NextResponse.json(response.data, { headers })
  } catch (error: any) {
    console.warn('Geocoding proxy failed:', error.message)
    return NextResponse.json([], { status: 200 }) // Fail silently, return empty list
  }
}
