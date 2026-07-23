import { redirect } from 'next/navigation'
import React from 'react'
import { auth } from '@/lib/auth'
import { SidebarProvider } from "@/context/SidebarContext"
import OwnerLayoutContentWrapper from "@/components/owner/dashboard/layout/OwnerLayoutContentWrapper"

export const dynamic = 'force-dynamic'

interface OwnerLayoutProps {
  children: React.ReactNode
}

export default async function OwnerLayout({ children }: OwnerLayoutProps) {
  const session = await auth()

  if (!session?.user?.id) {
    redirect('/login?callbackUrl=/owner/dashboard')
  }

  const role = (session.user as any).role
  if (role !== 'owner' && role !== 'agent' && role !== 'admin') {
    redirect('/unauthorized?required=owner_agent')
  }

  return (
    <SidebarProvider>
      <OwnerLayoutContentWrapper>
        {children}
      </OwnerLayoutContentWrapper>
    </SidebarProvider>
  )
}
