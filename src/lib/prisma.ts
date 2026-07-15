import { PrismaClient } from '@prisma/client'

// Monkey-patch BigInt to support JSON.stringify serialization
if (typeof BigInt !== 'undefined' && !(BigInt.prototype as any).toJSON) {
  ;(BigInt.prototype as any).toJSON = function () {
    return this.toString()
  }
}

let dbUrl = process.env.DATABASE_URL || ''
if (dbUrl && (dbUrl.includes('-pooler') || dbUrl.includes('pooler')) && !dbUrl.includes('pgbouncer=')) {
  dbUrl += dbUrl.includes('?') ? '&pgbouncer=true' : '?pgbouncer=true'
}

const globalForPrisma = globalThis as unknown as { prisma: PrismaClient }

export const prisma = globalForPrisma.prisma ?? new PrismaClient({
  datasourceUrl: dbUrl || undefined
})

if (process.env.NODE_ENV !== 'production') globalForPrisma.prisma = prisma
