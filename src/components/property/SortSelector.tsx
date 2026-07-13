'use client'

import { useRouter, useSearchParams } from 'next/navigation'

interface SortSelectorProps {
  currentSort: string
}

export default function SortSelector({ currentSort }: SortSelectorProps) {
  const router = useRouter()
  const searchParams = useSearchParams()

  const handleSortChange = (value: string) => {
    const params = new URLSearchParams(searchParams.toString())
    params.set('sort', value)
    router.push(`/listings?${params.toString()}`)
  }

  return (
    <div className="flex items-center space-x-3 w-full sm:w-auto justify-between sm:justify-start">
      <label className="text-xs font-bold text-slate-500 whitespace-nowrap">Sắp xếp theo:</label>
      <div className="relative min-w-[150px]">
        <select 
          name="sort" 
          value={currentSort}
          onChange={(e) => handleSortChange(e.target.value)}
          className="w-full pl-3 pr-8 py-2 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-lg text-xs font-semibold outline-none appearance-none cursor-pointer transition"
        >
          <option value="latest">Mới nhất</option>
          <option value="price_asc">Giá tăng dần</option>
          <option value="price_desc">Giá giảm dần</option>
          <option value="area_asc">Diện tích tăng dần</option>
          <option value="area_desc">Diện tích giảm dần</option>
        </select>
        <i className="fa-solid fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-[10px]"></i>
      </div>
    </div>
  )
}
