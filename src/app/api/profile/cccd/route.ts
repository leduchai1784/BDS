import { NextResponse } from 'next/server'
import { auth } from '@/lib/auth'
import { prisma } from '@/lib/prisma'
import { updateNksCccd, updateNksInfo, getNksUserInfo } from '@/lib/nks'

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

    // 1. Update local database
    const localUpdateData: any = {
      dob,
      pob,
      idNumber: id_number,
      idDate: id_date,
      idPlace: id_place,
      permanentAddress: permanent_address
    }

    if (cccd_front) localUpdateData.cccdFront = cccd_front
    if (cccd_back) localUpdateData.cccdBack = cccd_back

    const updatedUser = await prisma.user.update({
      where: { id: userId },
      data: localUpdateData
    })

    // 2. Sync to NKS if user has NKS Token
    if (updatedUser.nksToken) {
      // Helper base64 getter
      const getBase64 = (inputBase64: string | undefined, existingPath: string | null) => {
        if (inputBase64) return inputBase64
        if (!existingPath) return ''
        if (existingPath.startsWith('data:image')) return existingPath
        
        // Wait, if it's already an NKS URL, return as is
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

      // If new images are uploaded, call updateCccd
      if (cccd_front || cccd_back) {
        const nksCccdData = {
          cccd_front: getBase64(cccd_front, updatedUser.cccdFront),
          cccd_back: getBase64(cccd_back, updatedUser.cccdBack),
          id_number: id_number,
          id_date: nksIdDate,
          id_place: id_place
        }

        await updateNksCccd(updatedUser.nksToken, nksCccdData).catch(err => {
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

      await updateNksInfo(updatedUser.nksToken, updatedUser, nksInfoData).catch(err => {
        console.warn('Failed to sync CCCD text to NKS info:', err.message)
      })

      // Fetch fresh NKS info to pull saved CCCD image URLs back
      const nksUserInfoRes = await getNksUserInfo(updatedUser.nksToken).catch(() => null)
      if (nksUserInfoRes?.success && nksUserInfoRes.data) {
        const syncUrls: any = {}
        if (nksUserInfoRes.data.cccd_front) {
          syncUrls.cccdFront = nksUserInfoRes.data.cccd_front
        }
        if (nksUserInfoRes.data.cccd_back) {
          syncUrls.cccdBack = nksUserInfoRes.data.cccd_back
        }

        if (Object.keys(syncUrls).length > 0) {
          await prisma.user.update({
            where: { id: userId },
            data: syncUrls
          })
        }
      }
    }

    return NextResponse.json({
      success: true,
      message: 'CCCD details updated successfully'
    })
  } catch (error: any) {
    console.error('Update CCCD error:', error)
    return NextResponse.json({ error: error.message }, { status: 500 })
  }
}
