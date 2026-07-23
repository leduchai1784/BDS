import { redirect } from 'next/navigation'
import React from 'react'
import { auth } from '@/lib/auth'
import { SidebarProvider } from "@/context/SidebarContext"
import AdminLayoutContentWrapper from "./AdminLayoutContentWrapper"

export const dynamic = 'force-dynamic'

interface AdminLayoutProps {
  children: React.ReactNode
}

export default async function AdminLayout({ children }: AdminLayoutProps) {
  const session = await auth()

  if (!session?.user?.id) {
    redirect('/login?callbackUrl=/admin')
  }

  if (session.user.role !== 'admin') {
    redirect('/')
  }

  return (
    <SidebarProvider>
      <AdminLayoutContentWrapper>
        {children}
      </AdminLayoutContentWrapper>
    </SidebarProvider>
  )
}
