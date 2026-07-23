import { redirect } from 'next/navigation'

export const dynamic = 'force-dynamic'

export default async function OldPropertyEditRedirectPage({
  params
}: {
  params: Promise<{ id: string }>
}) {
  const resolvedParams = await params
  redirect(`/owner/properties/${resolvedParams.id}/edit`)
}
