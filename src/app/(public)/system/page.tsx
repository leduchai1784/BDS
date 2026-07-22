import { redirect } from 'next/navigation'
import { auth } from '@/lib/auth'

export const dynamic = 'force-dynamic'

export default async function SystemRedirectPage() {
  const session = await auth()
  
  if (!session?.user) {
    redirect('/login?callbackUrl=/system')
  }

  const role = (session.user as any).role

  if (role === 'admin') {
    redirect('/admin/dashboard')
  }
  
  if (role === 'owner' || role === 'agent') {
    redirect('/profile?tab=properties')
  }

  // Mặc định cho tenant (khách thuê)
  redirect('/profile?tab=register_owner')
}
