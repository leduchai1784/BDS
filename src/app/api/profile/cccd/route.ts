import { NextResponse } from 'next/server'
import { auth } from '@/lib/auth'
import { prisma } from '@/lib/prisma'
import { updateNksCccd, updateNksInfo, getNksUserInfo } from '@/lib/nks'
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

  // Send as JSON body instead of URLSearchParams to prevent encoding/parsing mismatch (e.g. Display name cannot contain slashes)
  const response = await fetch(`https://api.cloudinary.com/v1_1/${cloudName}/image/upload`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      file: `data:image/jpeg;base64,${cleanBase64}`,
      api_key: apiKey,
      timestamp: timestamp,
      signature: signature
    })
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
    const body = await req.json()

    const { 
      dob, pob, id_number, id_date, id_place, permanent_address, 
      cccd_front, cccd_back 
    } = body

    if (!dob || !pob || !id_number || !id_date || !id_place || !permanent_address) {
      return NextResponse.json({ error: 'Missing required parameters' }, { status: 400 })
    }

    // Retrieve user to check for NKS token
    const user = await prisma.user.findUnique({
      where: { id: userId }
    })

    const hasNks = !!user?.nksToken

    // 1. Prepare local database update fields
    const localUpdateData: any = {
      dob,
      pob,
      idNumber: id_number,
      idDate: id_date,
      idPlace: id_place,
      permanentAddress: permanent_address
    }

    // Check if Cloudinary is configured
    const hasCloudinary = !!(process.env.CLOUDINARY_CLOUD_NAME && process.env.CLOUDINARY_API_KEY && process.env.CLOUDINARY_API_SECRET)

    // If user has an NKS token (api account), bypass Cloudinary upload to save time and API quota.
    // NKS will host the images and return URLs which we sync back.
    if (cccd_front) {
      if (cccd_front.startsWith('data:image')) {
        if (hasNks) {
          // NKS will handle the image; keep existing local URL for now (we sync from NKS later)
          localUpdateData.cccdFront = user?.cccdFront
        } else if (hasCloudinary) {
          try {
            localUpdateData.cccdFront = await uploadToCloudinary(cccd_front)
          } catch (e: any) {
            console.warn('Cloudinary upload failed for cccd_front, storing base64 locally:', e.message)
            localUpdateData.cccdFront = cccd_front
          }
        } else {
          // No Cloudinary and no NKS — save base64 locally
          localUpdateData.cccdFront = cccd_front
        }
      } else {
        localUpdateData.cccdFront = cccd_front
      }
    }

    if (cccd_back) {
      if (cccd_back.startsWith('data:image')) {
        if (hasNks) {
          localUpdateData.cccdBack = user?.cccdBack
        } else if (hasCloudinary) {
          try {
            localUpdateData.cccdBack = await uploadToCloudinary(cccd_back)
          } catch (e: any) {
            console.warn('Cloudinary upload failed for cccd_back, storing base64 locally:', e.message)
            localUpdateData.cccdBack = cccd_back
          }
        } else {
          // No Cloudinary and no NKS — save base64 locally
          localUpdateData.cccdBack = cccd_back
        }
      } else {
        localUpdateData.cccdBack = cccd_back
      }
    }

    let updatedUser = await prisma.user.update({
      where: { id: userId },
      data: localUpdateData
    })

    // 2. Sync to NKS if user has NKS Token
    if (hasNks && user?.nksToken) {
      // Helper base64 getter
      const getBase64 = (inputBase64: string | undefined, existingPath: string | null) => {
        if (inputBase64) return inputBase64
        if (!existingPath) return ''
        return existingPath
      }

      // Convert id_date format for NKS
      let nksIdDate = id_date
      if (nksIdDate.includes('/')) {
        const parts = nksIdDate.split('/')
        if (parts.length === 3) {
          nksIdDate = `${parts[2]}-${parts[1].padStart(2, '0')}-${parts[0].padStart(2, '0')}`
        }
      }

      let nksDob = dob
      if (nksDob.includes('/')) {
        const parts = nksDob.split('/')
        if (parts.length === 3) {
          nksDob = `${parts[2]}-${parts[1].padStart(2, '0')}-${parts[0].padStart(2, '0')}`
        }
      }

      // If new images are uploaded, call updateCccd with raw base64 data
      if (cccd_front || cccd_back) {
        const nksCccdData = {
          cccd_front: getBase64(cccd_front, updatedUser.cccdFront),
          cccd_back: getBase64(cccd_back, updatedUser.cccdBack),
          id_number: id_number,
          id_date: nksIdDate,
          id_place: id_place
        }

        await updateNksCccd(user.nksToken, nksCccdData).catch(err => {
          console.warn('Failed to update CCCD on NKS:', err.message)
        })
      }

      // Sync texts to NKS
      const nksInfoData = {
        dob: nksDob,
        pob,
        permanent_address,
        id_number: id_number,
        id_date: nksIdDate,
        id_place: id_place
      }

      await updateNksInfo(user.nksToken, updatedUser, nksInfoData).catch(err => {
        console.warn('Failed to sync CCCD text to NKS info:', err.message)
      })

      // Fetch fresh NKS info to pull saved CCCD image URLs back
      const nksUserInfoRes = await getNksUserInfo(user.nksToken).catch(() => null)
      if (nksUserInfoRes?.success && nksUserInfoRes.data) {
        const syncUrls: any = {}
        if (nksUserInfoRes.data.cccd_front) {
          syncUrls.cccdFront = nksUserInfoRes.data.cccd_front
        }
        if (nksUserInfoRes.data.cccd_back) {
          syncUrls.cccdBack = nksUserInfoRes.data.cccd_back
        }

        if (Object.keys(syncUrls).length > 0) {
          updatedUser = await prisma.user.update({
            where: { id: userId },
            data: syncUrls
          })
        }
      }
    }

    return NextResponse.json({
      success: true,
      user: {
        idNumber: updatedUser.idNumber,
        idDate: updatedUser.idDate,
        idPlace: updatedUser.idPlace,
        dob: updatedUser.dob,
        pob: updatedUser.pob,
        permanentAddress: updatedUser.permanentAddress,
        cccdFront: updatedUser.cccdFront,
        cccdBack: updatedUser.cccdBack
      },
      message: 'CCCD details updated successfully'
    })
  } catch (error: any) {
    console.error('Update CCCD error:', error)
    return NextResponse.json({ error: error.message }, { status: 500 })
  }
}
