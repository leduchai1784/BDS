'use client'

interface MapFilterBarProps {
  purpose: string
  setPurpose: (p: string) => void
  propertyType: string
  setPropertyType: (t: string) => void
  price: string
  setPrice: (pr: string) => void
  area: string
  setArea: (a: string) => void
  onReset: () => void
}

export default function MapFilterBar({
  purpose,
  setPurpose,
  propertyType,
  setPropertyType,
  price,
  setPrice,
  area,
  setArea,
  onReset
}: MapFilterBarProps) {
  return (
    <div className="flex flex-wrap items-center gap-2 bg-white/95 backdrop-blur-md p-3.5 rounded-2xl shadow-sm border border-slate-100 w-full mb-4">
      {/* 1. Purpose */}
      <select
        value={purpose}
        onChange={(e) => { setPurpose(e.target.value); setPrice('') }}
        className="px-3 py-2 bg-slate-50 border border-slate-200 text-slate-700 rounded-xl text-xs font-bold focus:outline-none focus:border-primary cursor-pointer transition"
      >
        <option value="">Giao dịch (Tất cả)</option>
        <option value="rent">Cho thuê</option>
        <option value="sale">Mua bán</option>
      </select>

      {/* 2. Property Type */}
      <select
        value={propertyType}
        onChange={(e) => setPropertyType(e.target.value)}
        className="px-3 py-2 bg-slate-50 border border-slate-200 text-slate-700 rounded-xl text-xs font-bold focus:outline-none focus:border-primary cursor-pointer transition"
      >
        <option value="">Loại hình (Tất cả)</option>
        <option value="apartment">Căn hộ</option>
        <option value="house">Nhà riêng</option>
        <option value="room">Phòng trọ</option>
        <option value="land">Đất nền</option>
        <option value="premises">Mặt bằng</option>
        <option value="office">Văn phòng</option>
        <option value="warehouse">Kho xưởng</option>
      </select>

      {/* 3. Price */}
      <select
        value={price}
        onChange={(e) => setPrice(e.target.value)}
        className="px-3 py-2 bg-slate-50 border border-slate-200 text-slate-700 rounded-xl text-xs font-bold focus:outline-none focus:border-primary cursor-pointer transition"
      >
        <option value="">Mức giá (Tất cả)</option>
        {(purpose === 'rent' || purpose === '') && (
          <>
            <option value="under_3">Dưới 3 triệu</option>
            <option value="3_5">3 - 5 triệu</option>
            <option value="5_10">5 - 10 triệu</option>
            <option value="10_20">10 - 20 triệu</option>
            <option value="above_20">Trên 20 triệu</option>
          </>
        )}
        {purpose === 'sale' && (
          <>
            <option value="under_1b">Dưới 1 tỷ</option>
            <option value="1b_3b">1 - 3 tỷ</option>
            <option value="3b_5b">3 - 5 tỷ</option>
            <option value="5b_10b">5 - 10 tỷ</option>
            <option value="above_10b">Trên 10 tỷ</option>
          </>
        )}
      </select>

      {/* 4. Area */}
      <select
        value={area}
        onChange={(e) => setArea(e.target.value)}
        className="px-3 py-2 bg-slate-50 border border-slate-200 text-slate-700 rounded-xl text-xs font-bold focus:outline-none focus:border-primary cursor-pointer transition"
      >
        <option value="">Diện tích (Tất cả)</option>
        <option value="under_30">Dưới 30 m²</option>
        <option value="30_50">30 - 50 m²</option>
        <option value="50_80">50 - 80 m²</option>
        <option value="80_120">80 - 120 m²</option>
        <option value="above_120">Trên 120 m²</option>
      </select>

      {/* Reset */}
      <button
        onClick={onReset}
        type="button"
        className="ml-auto px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold cursor-pointer transition flex items-center gap-1.5"
      >
        <i className="fa-solid fa-arrow-rotate-left text-[10px]"></i>
        <span>Đặt lại</span>
      </button>
    </div>
  )
}
