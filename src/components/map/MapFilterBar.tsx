'use client'

import { useState } from 'react'

interface MapFilterBarProps {
  keyword: string
  setKeyword: (k: string) => void
  purpose: string
  setPurpose: (p: string) => void
  propertyType: string
  setPropertyType: (t: string) => void
  price: string
  setPrice: (pr: string) => void
  bedrooms: string
  setBedrooms: (b: string) => void
  area: string
  setArea: (a: string) => void
  bathrooms: string
  setBathrooms: (b: string) => void
  furniture: string
  setFurniture: (f: string) => void
  direction: string
  setDirection: (d: string) => void
  onReset: () => void
}

const propertyTypeLabels: Record<string, string> = {
  '': 'Loại hình (Tất cả)',
  'apartment': 'Căn hộ',
  'house': 'Nhà riêng',
  'room': 'Phòng trọ',
  'land': 'Đất nền',
  'premises': 'Mặt bằng',
  'office': 'Văn phòng',
  'warehouse': 'Kho xưởng'
}

function getPropertyTypeLabel(key: string): string {
  return propertyTypeLabels[key] || 'Loại hình'
}

function getPriceLabel(key: string, purpose: string): string {
  if (!key) return 'Mức giá'
  if (purpose === 'sale') {
    const labels: Record<string, string> = {
      'under_1b': 'Dưới 1 tỷ',
      '1b_3b': '1 - 3 tỷ',
      '3b_5b': '3 - 5 tỷ',
      '5b_10b': '5 - 10 tỷ',
      'above_10b': 'Trên 10 tỷ'
    }
    return labels[key] || 'Mức giá'
  } else {
    const labels: Record<string, string> = {
      'under_3': 'Dưới 3 triệu',
      '3_5': '3 - 5 triệu',
      '5_10': '5 - 10 triệu',
      '10_20': '10 - 20 triệu',
      'above_20': 'Trên 20 triệu'
    }
    return labels[key] || 'Mức giá'
  }
}

function getBedroomsLabel(key: string): string {
  if (!key) return 'Phòng ngủ'
  const labels: Record<string, string> = {
    '1': '1 Phòng ngủ',
    '2': '2 Phòng ngủ',
    '3': '3 Phòng ngủ',
    '4_plus': '4+ Phòng ngủ'
  }
  return labels[key] || 'Phòng ngủ'
}

function getAreaLabel(key: string): string {
  if (!key) return 'Diện tích'
  const labels: Record<string, string> = {
    'under_30': 'Dưới 30 m²',
    '30_50': '30 - 50 m²',
    '50_80': '50 - 80 m²',
    '80_120': '80 - 120 m²',
    'above_120': 'Trên 120 m²'
  }
  return labels[key] || 'Diện tích'
}

export default function MapFilterBar({
  keyword,
  setKeyword,
  purpose,
  setPurpose,
  propertyType,
  setPropertyType,
  price,
  setPrice,
  bedrooms,
  setBedrooms,
  area,
  setArea,
  bathrooms,
  setBathrooms,
  furniture,
  setFurniture,
  direction,
  setDirection,
  onReset
}: MapFilterBarProps) {
  const [activeDropdown, setActiveDropdown] = useState<string | null>(null)

  return (
    <div className="relative flex flex-wrap items-center gap-2 bg-white/95 backdrop-blur-md p-3 rounded-2xl shadow-sm border border-slate-100 w-full z-40">
      
      {/* Backdrop to close active dropdown */}
      {activeDropdown && (
        <div 
          className="fixed inset-0 z-40 bg-transparent"
          onClick={() => setActiveDropdown(null)}
        />
      )}

      {/* 0. Search Keyword Input */}
      <div className="relative flex items-center bg-slate-50 border border-slate-200 rounded-full px-3.5 py-1.5 h-10 w-full sm:w-64 z-50">
        <i className="fa-solid fa-magnifying-glass text-slate-400 text-xs mr-2"></i>
        <input 
          type="text"
          value={keyword}
          onChange={(e) => setKeyword(e.target.value)}
          placeholder="Tìm địa điểm, dự án..."
          className="bg-transparent border-none text-xs font-semibold outline-none w-full text-slate-800 placeholder-slate-400"
        />
        {keyword && (
          <button type="button" onClick={() => setKeyword('')} className="text-slate-400 hover:text-slate-650 cursor-pointer pl-1.5">
            <i className="fa-solid fa-xmark text-xs"></i>
          </button>
        )}
      </div>

      {/* 1. Purpose */}
      <div className="relative z-50">
        <button
          type="button"
          onClick={() => setActiveDropdown(activeDropdown === 'purpose' ? null : 'purpose')}
          className={`flex items-center justify-between space-x-1.5 px-4 py-2 border rounded-full text-xs font-bold transition cursor-pointer h-10 min-w-[120px] ${
            purpose ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100'
          }`}
        >
          <span>{purpose === 'rent' ? 'Cho thuê' : purpose === 'sale' ? 'Mua bán' : 'Giao dịch'}</span>
          <i className={`fa-solid fa-chevron-down text-[8px] transition duration-200 ${activeDropdown === 'purpose' ? 'rotate-180 text-primary' : 'text-slate-400'}`}></i>
        </button>
        {activeDropdown === 'purpose' && (
          <div className="absolute left-0 mt-2 w-44 rounded-2xl bg-white border border-slate-150 shadow-2xl p-3 z-50 text-left flex flex-col space-y-1">
            <button type="button" onClick={() => { setPurpose(''); setPrice(''); setActiveDropdown(null) }} className={`w-full text-left px-3 py-2 rounded-xl text-xs font-bold transition cursor-pointer ${!purpose ? 'bg-primary/5 text-primary' : 'text-slate-700 hover:bg-slate-50'}`}>Tất cả giao dịch</button>
            <button type="button" onClick={() => { setPurpose('rent'); setPrice(''); setActiveDropdown(null) }} className={`w-full text-left px-3 py-2 rounded-xl text-xs font-bold transition cursor-pointer ${purpose === 'rent' ? 'bg-primary/5 text-primary' : 'text-slate-700 hover:bg-slate-50'}`}>Cho thuê</button>
            <button type="button" onClick={() => { setPurpose('sale'); setPrice(''); setActiveDropdown(null) }} className={`w-full text-left px-3 py-2 rounded-xl text-xs font-bold transition cursor-pointer ${purpose === 'sale' ? 'bg-primary/5 text-primary' : 'text-slate-700 hover:bg-slate-50'}`}>Mua bán</button>
          </div>
        )}
      </div>

      {/* 2. Property Type */}
      <div className="relative z-50">
        <button
          type="button"
          onClick={() => setActiveDropdown(activeDropdown === 'type' ? null : 'type')}
          className={`flex items-center justify-between space-x-1.5 px-4 py-2 border rounded-full text-xs font-bold transition cursor-pointer h-10 min-w-[120px] ${
            propertyType ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100'
          }`}
        >
          <span>{getPropertyTypeLabel(propertyType)}</span>
          <i className={`fa-solid fa-chevron-down text-[8px] transition duration-200 ${activeDropdown === 'type' ? 'rotate-180 text-primary' : 'text-slate-400'}`}></i>
        </button>
        {activeDropdown === 'type' && (
          <div className="absolute left-0 mt-2 w-48 rounded-2xl bg-white border border-slate-150 shadow-2xl p-3 z-50 text-left flex flex-col space-y-1 max-h-72 overflow-y-auto">
            {Object.entries(propertyTypeLabels).map(([key, label]) => (
              <button 
                key={key} 
                type="button" 
                onClick={() => { setPropertyType(key); setActiveDropdown(null) }} 
                className={`w-full text-left px-3 py-2 rounded-xl text-xs font-bold transition cursor-pointer ${propertyType === key ? 'bg-primary/5 text-primary' : 'text-slate-700 hover:bg-slate-50'}`}
              >
                {label}
              </button>
            ))}
          </div>
        )}
      </div>

      {/* 3. Price */}
      <div className="relative z-50">
        <button
          type="button"
          onClick={() => setActiveDropdown(activeDropdown === 'price' ? null : 'price')}
          className={`flex items-center justify-between space-x-1.5 px-4 py-2 border rounded-full text-xs font-bold transition cursor-pointer h-10 min-w-[120px] ${
            price ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100'
          }`}
        >
          <span>{getPriceLabel(price, purpose)}</span>
          <i className={`fa-solid fa-chevron-down text-[8px] transition duration-200 ${activeDropdown === 'price' ? 'rotate-180 text-primary' : 'text-slate-400'}`}></i>
        </button>
        {activeDropdown === 'price' && (
          <div className="absolute left-0 mt-2 w-64 rounded-2xl bg-white border border-slate-150 shadow-2xl p-4 z-50 text-left">
            <span className="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2.5 px-0.5">Khoảng giá</span>
            <div className="grid grid-cols-2 gap-2">
              <button type="button" onClick={() => setPrice('')} className={`px-2 py-1.5 border rounded-xl text-center text-[11px] font-bold transition cursor-pointer col-span-2 ${!price ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'}`}>Tất cả mức giá</button>
              {(purpose === 'rent' || purpose === '') && (
                <>
                  <button type="button" onClick={() => setPrice('under_3')} className={`px-2 py-1.5 border rounded-xl text-center text-[11px] font-bold transition cursor-pointer ${price === 'under_3' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'}`}>Dưới 3 triệu</button>
                  <button type="button" onClick={() => setPrice('3_5')} className={`px-2 py-1.5 border rounded-xl text-center text-[11px] font-bold transition cursor-pointer ${price === '3_5' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'}`}>3 - 5 triệu</button>
                  <button type="button" onClick={() => setPrice('5_10')} className={`px-2 py-1.5 border rounded-xl text-center text-[11px] font-bold transition cursor-pointer ${price === '5_10' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'}`}>5 - 10 triệu</button>
                  <button type="button" onClick={() => setPrice('10_20')} className={`px-2 py-1.5 border rounded-xl text-center text-[11px] font-bold transition cursor-pointer ${price === '10_20' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'}`}>10 - 20 triệu</button>
                  <button type="button" onClick={() => setPrice('above_20')} className={`px-2 py-1.5 border rounded-xl text-center text-[11px] font-bold transition cursor-pointer col-span-2 ${price === 'above_20' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'}`}>Trên 20 triệu</button>
                </>
              )}
              {purpose === 'sale' && (
                <>
                  <button type="button" onClick={() => setPrice('under_1b')} className={`px-2 py-1.5 border rounded-xl text-center text-[11px] font-bold transition cursor-pointer ${price === 'under_1b' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'}`}>Dưới 1 tỷ</button>
                  <button type="button" onClick={() => setPrice('1b_3b')} className={`px-2 py-1.5 border rounded-xl text-center text-[11px] font-bold transition cursor-pointer ${price === '1b_3b' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'}`}>1 - 3 tỷ</button>
                  <button type="button" onClick={() => setPrice('3b_5b')} className={`px-2 py-1.5 border rounded-xl text-center text-[11px] font-bold transition cursor-pointer ${price === '3b_5b' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'}`}>3 - 5 tỷ</button>
                  <button type="button" onClick={() => setPrice('5b_10b')} className={`px-2 py-1.5 border rounded-xl text-center text-[11px] font-bold transition cursor-pointer ${price === '5b_10b' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'}`}>5 - 10 tỷ</button>
                  <button type="button" onClick={() => setPrice('above_10b')} className={`px-2 py-1.5 border rounded-xl text-center text-[11px] font-bold transition cursor-pointer col-span-2 ${price === 'above_10b' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'}`}>Trên 10 tỷ</button>
                </>
              )}
            </div>
            <div className="flex justify-between items-center border-t border-slate-100 pt-2.5 mt-3.5">
              <button type="button" onClick={() => { setPrice(''); setActiveDropdown(null) }} className="text-[10px] text-slate-400 font-bold hover:text-slate-650 cursor-pointer">Xóa</button>
              <button type="button" onClick={() => setActiveDropdown(null)} className="bg-primary text-white text-[10px] font-bold px-3 py-1.5 rounded-lg hover:bg-primary-hover transition cursor-pointer">Áp dụng</button>
            </div>
          </div>
        )}
      </div>

      {/* 4. Bedrooms */}
      <div className="relative z-50">
        <button
          type="button"
          onClick={() => setActiveDropdown(activeDropdown === 'bedrooms' ? null : 'bedrooms')}
          className={`flex items-center justify-between space-x-1.5 px-4 py-2 border rounded-full text-xs font-bold transition cursor-pointer h-10 min-w-[120px] ${
            bedrooms ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100'
          }`}
        >
          <span>{getBedroomsLabel(bedrooms)}</span>
          <i className={`fa-solid fa-chevron-down text-[8px] transition duration-200 ${activeDropdown === 'bedrooms' ? 'rotate-180 text-primary' : 'text-slate-400'}`}></i>
        </button>
        {activeDropdown === 'bedrooms' && (
          <div className="absolute left-0 mt-2 w-48 rounded-2xl bg-white border border-slate-150 shadow-2xl p-3.5 z-50 text-left flex flex-col space-y-1">
            <span className="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2 px-0.5">Số phòng ngủ</span>
            <button type="button" onClick={() => { setBedrooms(''); setActiveDropdown(null) }} className={`w-full text-left px-3 py-2 rounded-xl text-xs font-bold transition cursor-pointer ${!bedrooms ? 'bg-primary/5 text-primary' : 'text-slate-700 hover:bg-slate-50'}`}>Tất cả phòng ngủ</button>
            <button type="button" onClick={() => { setBedrooms('1'); setActiveDropdown(null) }} className={`w-full text-left px-3 py-2 rounded-xl text-xs font-bold transition cursor-pointer ${bedrooms === '1' ? 'bg-primary/5 text-primary' : 'text-slate-700 hover:bg-slate-50'}`}>1 Phòng ngủ</button>
            <button type="button" onClick={() => { setBedrooms('2'); setActiveDropdown(null) }} className={`w-full text-left px-3 py-2 rounded-xl text-xs font-bold transition cursor-pointer ${bedrooms === '2' ? 'bg-primary/5 text-primary' : 'text-slate-700 hover:bg-slate-50'}`}>2 Phòng ngủ</button>
            <button type="button" onClick={() => { setBedrooms('3'); setActiveDropdown(null) }} className={`w-full text-left px-3 py-2 rounded-xl text-xs font-bold transition cursor-pointer ${bedrooms === '3' ? 'bg-primary/5 text-primary' : 'text-slate-700 hover:bg-slate-50'}`}>3 Phòng ngủ</button>
            <button type="button" onClick={() => { setBedrooms('4_plus'); setActiveDropdown(null) }} className={`w-full text-left px-3 py-2 rounded-xl text-xs font-bold transition cursor-pointer ${bedrooms === '4_plus' ? 'bg-primary/5 text-primary' : 'text-slate-700 hover:bg-slate-50'}`}>4+ Phòng ngủ</button>
          </div>
        )}
      </div>

      {/* 5. Area */}
      <div className="relative z-50">
        <button
          type="button"
          onClick={() => setActiveDropdown(activeDropdown === 'area' ? null : 'area')}
          className={`flex items-center justify-between space-x-1.5 px-4 py-2 border rounded-full text-xs font-bold transition cursor-pointer h-10 min-w-[120px] ${
            area ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100'
          }`}
        >
          <span>{getAreaLabel(area)}</span>
          <i className={`fa-solid fa-chevron-down text-[8px] transition duration-200 ${activeDropdown === 'area' ? 'rotate-180 text-primary' : 'text-slate-400'}`}></i>
        </button>
        {activeDropdown === 'area' && (
          <div className="absolute left-0 mt-2 w-52 rounded-2xl bg-white border border-slate-150 shadow-2xl p-3.5 z-50 text-left flex flex-col space-y-1">
            <span className="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2 px-0.5">Chọn diện tích</span>
            <button type="button" onClick={() => { setArea(''); setActiveDropdown(null) }} className={`w-full text-left px-3 py-2 rounded-xl text-xs font-bold transition cursor-pointer ${!area ? 'bg-primary/5 text-primary' : 'text-slate-700 hover:bg-slate-50'}`}>Tất cả diện tích</button>
            <button type="button" onClick={() => { setArea('under_30'); setActiveDropdown(null) }} className={`w-full text-left px-3 py-2 rounded-xl text-xs font-bold transition cursor-pointer ${area === 'under_30' ? 'bg-primary/5 text-primary' : 'text-slate-700 hover:bg-slate-50'}`}>Dưới 30 m²</button>
            <button type="button" onClick={() => { setArea('30_50'); setActiveDropdown(null) }} className={`w-full text-left px-3 py-2 rounded-xl text-xs font-bold transition cursor-pointer ${area === '30_50' ? 'bg-primary/5 text-primary' : 'text-slate-700 hover:bg-slate-50'}`}>30 - 50 m²</button>
            <button type="button" onClick={() => { setArea('50_80'); setActiveDropdown(null) }} className={`w-full text-left px-3 py-2 rounded-xl text-xs font-bold transition cursor-pointer ${area === '50_80' ? 'bg-primary/5 text-primary' : 'text-slate-700 hover:bg-slate-50'}`}>50 - 80 m²</button>
            <button type="button" onClick={() => { setArea('80_120'); setActiveDropdown(null) }} className={`w-full text-left px-3 py-2 rounded-xl text-xs font-bold transition cursor-pointer ${area === '80_120' ? 'bg-primary/5 text-primary' : 'text-slate-700 hover:bg-slate-50'}`}>80 - 120 m²</button>
            <button type="button" onClick={() => { setArea('above_120'); setActiveDropdown(null) }} className={`w-full text-left px-3 py-2 rounded-xl text-xs font-bold transition cursor-pointer ${area === 'above_120' ? 'bg-primary/5 text-primary' : 'text-slate-700 hover:bg-slate-50'}`}>Trên 120 m²</button>
          </div>
        )}
      </div>

      {/* 6. Advanced Filters */}
      <div className="relative z-50">
        <button
          type="button"
          onClick={() => setActiveDropdown(activeDropdown === 'advanced' ? null : 'advanced')}
          className={`flex items-center justify-between space-x-1.5 px-4 py-2 border rounded-full text-xs font-bold transition cursor-pointer h-10 min-w-[120px] ${
            (bathrooms || furniture || direction) ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100'
          }`}
        >
          <span>Bộ lọc thêm</span>
          <i className={`fa-solid fa-chevron-down text-[8px] transition duration-200 ${activeDropdown === 'advanced' ? 'rotate-180 text-primary' : 'text-slate-400'}`}></i>
        </button>
        {activeDropdown === 'advanced' && (
          <div className="absolute right-0 mt-2 w-64 rounded-2xl bg-white border border-slate-150 shadow-2xl p-4 z-50 text-left flex flex-col space-y-3.5">
            {/* Bathrooms */}
            <div>
              <span className="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5 px-0.5">Số phòng tắm</span>
              <div className="flex gap-1.5">
                {['', '1', '2', '3_plus'].map((btn) => (
                  <button
                    key={btn}
                    type="button"
                    onClick={() => setBathrooms(btn)}
                    className={`flex-grow py-1 border rounded-lg text-center text-[10px] font-bold transition cursor-pointer ${
                      bathrooms === btn ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100'
                    }`}
                  >
                    {btn === '' ? 'Tất cả' : btn === '3_plus' ? '3+' : `${btn}`}
                  </button>
                ))}
              </div>
            </div>

            {/* Furniture */}
            <div>
              <span className="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5 px-0.5">Nội thất</span>
              <div className="flex gap-1.5">
                {[
                  { key: '', label: 'Tất cả' },
                  { key: 'full', label: 'Đầy đủ' },
                  { key: 'basic', label: 'Cơ bản' }
                ].map((item) => (
                  <button
                    key={item.key}
                    type="button"
                    onClick={() => setFurniture(item.key)}
                    className={`flex-grow py-1 border rounded-lg text-center text-[10px] font-bold transition cursor-pointer ${
                      furniture === item.key ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100'
                    }`}
                  >
                    {item.label}
                  </button>
                ))}
              </div>
            </div>

            {/* Direction */}
            <div>
              <span className="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5 px-0.5">Hướng nhà</span>
              <select
                value={direction}
                onChange={(e) => setDirection(e.target.value)}
                className="w-full px-3 py-1.5 bg-slate-50 border border-slate-200 text-slate-700 rounded-lg text-xs font-semibold outline-none transition cursor-pointer focus:border-primary focus:bg-white"
              >
                <option value="">Tất cả các hướng</option>
                <option value="Đông">Đông</option>
                <option value="Tây">Tây</option>
                <option value="Nam">Nam</option>
                <option value="Bắc">Bắc</option>
                <option value="Đông Nam">Đông Nam</option>
                <option value="Đông Bắc">Đông Bắc</option>
                <option value="Tây Nam">Tây Nam</option>
                <option value="Tây Bắc">Tây Bắc</option>
              </select>
            </div>

            <div className="flex justify-between items-center border-t border-slate-100 pt-2.5 mt-1">
              <button 
                type="button" 
                onClick={() => { setBathrooms(''); setFurniture(''); setDirection(''); setActiveDropdown(null) }} 
                className="text-[10px] text-slate-400 font-bold hover:text-slate-650 cursor-pointer"
              >
                Xóa lọc
              </button>
              <button 
                type="button" 
                onClick={() => setActiveDropdown(null)} 
                className="bg-primary text-white text-[10px] font-bold px-3 py-1.5 rounded-lg hover:bg-primary-hover transition cursor-pointer"
              >
                Áp dụng
              </button>
            </div>
          </div>
        )}
      </div>

      {/* Reset */}
      <button
        onClick={onReset}
        type="button"
        className="ml-auto px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold cursor-pointer transition flex items-center gap-1.5 z-50"
      >
        <i className="fa-solid fa-arrow-rotate-left text-[10px]"></i>
        <span>Đặt lại</span>
      </button>

    </div>
  )
}
