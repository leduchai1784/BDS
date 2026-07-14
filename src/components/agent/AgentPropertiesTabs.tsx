'use client'

import { useState } from 'react'
import PropertyCard from '@/components/property/PropertyCard'

interface PropertyItem {
  id: string
  title: string
  transactionType?: string | null
  priceLabel: string
  price: number | bigint | string
  area: number
  bedroom: number
  bathroom: number
  floors?: number | null
  address: string
  ward?: string | null
  district: string
  city: string
  isVip?: boolean
  isNew?: boolean
  propertyType?: string | null
  imagePath?: string | null
}

interface AgentPropertiesTabsProps {
  saleProperties: PropertyItem[]
  rentProperties: PropertyItem[]
  isCompany: boolean
}

export default function AgentPropertiesTabs({ saleProperties, rentProperties, isCompany }: AgentPropertiesTabsProps) {
  const [activeTab, setActiveTab] = useState<'rent' | 'sale'>('rent')

  const currentProperties = activeTab === 'rent' ? rentProperties : saleProperties
  const typeText = isCompany ? 'Doanh nghiệp' : 'Môi giới'

  return (
    <div className="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden mb-6 text-left">
      {/* Tabs Header */}
      <div className="flex border-b border-slate-100 bg-slate-50/60">
        <button
          onClick={() => setActiveTab('rent')}
          className={`flex-1 py-4 px-5 text-sm font-extrabold rounded-none transition duration-150 cursor-pointer flex items-center justify-center gap-2 ${
            activeTab === 'rent'
              ? 'bg-white text-primary border-b-2 border-primary shadow-sm'
              : 'text-slate-500 hover:text-slate-800'
          }`}
        >
          <i className="fa-solid fa-key"></i>
          Cho thuê
          <span className={`text-xs px-2 py-0.5 rounded-full font-black ${
            activeTab === 'rent' ? 'bg-primary/10 text-primary' : 'bg-slate-200/60 text-slate-500'
          }`}>
            {rentProperties.length}
          </span>
        </button>
        <button
          onClick={() => setActiveTab('sale')}
          className={`flex-1 py-4 px-5 text-sm font-extrabold rounded-none transition duration-150 cursor-pointer flex items-center justify-center gap-2 ${
            activeTab === 'sale'
              ? 'bg-white text-primary border-b-2 border-primary shadow-sm'
              : 'text-slate-500 hover:text-slate-800'
          }`}
        >
          <i className="fa-solid fa-tags"></i>
          Đang bán
          <span className={`text-xs px-2 py-0.5 rounded-full font-black ${
            activeTab === 'sale' ? 'bg-primary/10 text-primary' : 'bg-slate-200/60 text-slate-500'
          }`}>
            {saleProperties.length}
          </span>
        </button>
      </div>

      {/* Tab Body */}
      <div className="p-6 md:p-8">
        {currentProperties.length === 0 ? (
          <div className="text-center py-14 text-slate-400">
            <div className="w-14 h-14 rounded-full bg-slate-50 flex items-center justify-center mx-auto mb-3">
              <i className={activeTab === 'rent' ? "fa-solid fa-key text-xl text-slate-400" : "fa-solid fa-tags text-xl text-slate-400"}></i>
            </div>
            <h4 className="text-sm font-bold text-slate-700">
              {activeTab === 'rent' ? 'Chưa có tin cho thuê' : 'Chưa có tin đăng bán'}
            </h4>
            <p className="text-xs text-slate-400 mt-1">
              {typeText} này chưa đăng tin {activeTab === 'rent' ? 'cho thuê' : 'bán'} nào.
            </p>
          </div>
        ) : (
          <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
            {currentProperties.map(property => (
              <PropertyCard key={property.id} property={property} />
            ))}
          </div>
        )}
      </div>
    </div>
  )
}
