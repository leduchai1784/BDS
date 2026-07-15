import { NextResponse } from 'next/server'
import { auth } from '@/lib/auth'
import { prisma } from '@/lib/prisma'
import { updateNksAvatar, getNksUserInfo } from '@/lib/nks'
import crypto from 'crypto'

async function uploadToCloudinary(base64Image: string): Promise<string> {
  const cloudName = process.env.CLOUDINARY_CLOUD_NAME
  const apiKey = process.env.CLOUDINARY_API_KEY
  const apiSecret = process.env.CLOUDINARY_API_SECRET

  if (!cloudName || !apiKey || !apiSecret) {
    throw new Error('Cloudinary configuration is missing in environment variables')
  }

  // Clean base64 data header if present
  let cleanBase64 = base64Image
  if (base64Image.startsWith('data:image/')) {
    cleanBase64 = base64Image.substring(base64Image.indexOf(',') + 1)
  }

  const timestamp = Math.round(new Date().getTime() / 1000)
  const signatureStr = `timestamp=${timestamp}${apiSecret}`
  const signature = crypto.createHash('sha1').update(signatureStr).digest('hex')

  const formData = new URLSearchParams()
  formData.append('file', `data:image/jpeg;base64,${cleanBase64}`)
  formData.append('api_key', apiKey)
  formData.append('timestamp', String(timestamp))
  formData.append('signature', signature)

  const response = await fetch(`https://api.cloudinary.com/v1_1/${cloudName}/image/upload`, {
    method: 'POST',
    body: formData
  })

  if (!response.ok) {
    const errText = await response.text()
    throw new Error(`Cloudinary upload request failed: ${errText}`)
  }

  const result = await response.json()
  if (!result.secure_url) {
    throw new Error('No secure_url returned from Cloudinary')
  }

  return result.secure_url
}

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

    // 1. Upload base64 image to Cloudinary instead of saving locally (prevents EROFS error on Vercel)
    let finalAvatarPath = await uploadToCloudinary(avatar)

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
