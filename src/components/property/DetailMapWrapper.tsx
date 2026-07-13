'use client'

import dynamic from 'next/dynamic'

const DetailMap = dynamic(() => import('./DetailMap'), {
  ssr: false,
  loading: () => (
    <div className="w-full h-[250px] sm:h-[320px] bg-slate-100 flex items-center justify-center rounded-2xl border border-slate-200">
      <span className="text-xs text-slate-500 font-bold">Đang tải bản đồ vị trí...</span>
    </div>
  )
})

interface DetailMapWrapperProps {
  latitude: number
  longitude: number
  title: string
}

export default function DetailMapWrapper(props: DetailMapWrapperProps) {
  return <DetailMap {...props} />
}
