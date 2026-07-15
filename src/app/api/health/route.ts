import { NextResponse } from 'next/server'
import { prisma } from '@/lib/prisma'

export const dynamic = 'force-dynamic'

export async function GET() {
  try {
    // Test DB connection by counting users
    const userCount = await prisma.user.count()
    return NextResponse.json({ 
      success: true, 
      database: 'connected', 
      userCount,
      env: {
        hasDatabaseUrl: !!process.env.DATABASE_URL,
        hasAuthSecret: !!process.env.AUTH_SECRET,
        hasNextauthSecret: !!process.env.NEXTAUTH_SECRET,
        nodeEnv: process.env.NODE_ENV
      }
    })
  } catch (err: any) {
    console.error('Health check failed:', err)
    return NextResponse.json({ 
      success: false, 
      database: 'failed', 
      error: err.message,
      stack: err.stack,
      env: {
        hasDatabaseUrl: !!process.env.DATABASE_URL,
        hasAuthSecret: !!process.env.AUTH_SECRET,
        hasNextauthSecret: !!process.env.NEXTAUTH_SECRET,
        nodeEnv: process.env.NODE_ENV
      }
    })
  }
}
