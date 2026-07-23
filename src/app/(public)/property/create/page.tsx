import { redirect } from 'next/navigation'

export const dynamic = 'force-dynamic'

export default function OldPropertyCreateRedirectPage() {
  redirect('/owner/properties/create')
}
