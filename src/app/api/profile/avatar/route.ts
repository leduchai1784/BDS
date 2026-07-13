import { NextResponse } from 'next/server'
import { auth } from '@/lib/auth'
import { prisma } from '@/lib/prisma'
import { updateNksAvatar, getNksUserInfo } from '@/lib/nks'
import fs from 'fs'
import path from 'path'

export async function POST(req: Request) {
  try {
    const session = await auth()
    if (!session?.user?.id) {
      return NextResponse.json({ error: 'Unauthorized' }, { status: 401 })
    }

    const userId = Number(session.user.id)
    const { avatar } = await req.json()

    if (!avatar) {
      return NextResponse.json({ error: 'Missing avatar data' }, { status: 400 })
    }

    // Clean base64 data
    let base64Image = avatar
    if (avatar.startsWith('data:image/')) {
      base64Image = avatar.substring(avatar.indexOf(',') + 1)
    }

    // 1. Save locally to public/uploads/avatars
    const uploadsDir = path.join(process.cwd(), 'public', 'uploads', 'avatars')
    if (!fs.existsSync(uploadsDir)) {
      fs.mkdirSync(uploadsDir, { recursive: true })
    }

    const filename = `avatar-${userId}-${Date.now()}.jpg`
    const filePath = path.join(uploadsDir, filename)
    const buffer = Buffer.from(base64Image, 'base64')
    fs.writeFileSync(filePath, buffer)

    let finalAvatarPath = `/uploads/avatars/${filename}`

    // 2. Sync to NKS if user has NKS Token
    const user = await prisma.user.findUnique({
      where: { id: userId }
    })

    if (user?.nksToken) {
      const nksResult = await updateNksAvatar(user.nksToken, avatar).catch(err => {
        console.warn('Failed to upload avatar to NKS:', err.message)
        return null
      })

      if (nksResult?.success) {
        // Fetch hosted avatar URL from NKS
        const nksInfo = await getNksUserInfo(user.nksToken).catch(() => null)
        if (nksInfo?.success && nksInfo.data?.avatar) {
          finalAvatarPath = nksInfo.data.avatar
        }
      }
    }

    // Update avatar path in local database
    await prisma.user.update({
      where: { id: userId },
      data: { avatar: finalAvatarPath }
    })

    return NextResponse.json({
      success: true,
      avatar: finalAvatarPath,
      message: 'Avatar updated successfully'
    })
  } catch (error: any) {
    console.error('Update avatar error:', error)
    return NextResponse.json({ error: error.message }, { status: 500 })
  }
}
