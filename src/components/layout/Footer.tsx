'use client'

import Link from 'next/link'

export default function Footer() {
  const currentYear = new Date().getFullYear()

  const handleSubscribe = (e: React.FormEvent) => {
    e.preventDefault()
    alert('Cảm ơn bạn đã đăng ký nhận tin!')
  }

  return (
    <footer id="contact" className="bg-slate-950 text-slate-400 pt-16 pb-8 border-t border-slate-900 z-10 relative">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="grid grid-cols-1 md:grid-cols-12 gap-8 md:gap-12 pb-12 border-b border-slate-900">
          
          {/* Brand Column */}
          <div className="col-span-1 md:col-span-4 space-y-5">
            <Link href="/" className="flex items-center space-x-2">
              <div className="w-10 h-10 rounded-xl bg-primary flex items-center justify-center text-white shadow-lg shadow-primary/30">
                <i className="fa-solid fa-house-chimney text-lg"></i>
              </div>
              <span className="font-bold text-2xl tracking-tight text-white">
                BDS<span className="text-primary">Rental</span>
              </span>
            </Link>
            <p className="text-sm text-slate-400 leading-relaxed max-w-sm">
              BDS Rental là kênh thông tin bất động sản cho thuê trực thuộc Công ty TNHH Giải pháp Công nghệ Tri Thức Mới. Giúp kết nối nhanh chóng, an toàn giữa người thuê và chủ nhà.
            </p>
            <div className="flex space-x-3.5">
              <a href="#" className="w-9 h-9 rounded-xl bg-slate-900 hover:bg-primary hover:text-white flex items-center justify-center text-sm transition duration-200">
                <i className="fa-brands fa-facebook-f"></i>
              </a>
              <a href="#" className="w-9 h-9 rounded-xl bg-slate-900 hover:bg-primary hover:text-white flex items-center justify-center text-sm transition duration-200">
                <i className="fa-brands fa-youtube"></i>
              </a>
              <a href="#" className="w-9 h-9 rounded-xl bg-slate-900 hover:bg-primary hover:text-white flex items-center justify-center text-sm transition duration-200">
                <i className="fa-brands fa-tiktok"></i>
              </a>
              <a href="#" className="w-9 h-9 rounded-xl bg-slate-900 hover:bg-primary hover:text-white flex items-center justify-center text-sm transition duration-200">
                <i className="fa-solid fa-envelope"></i>
              </a>
            </div>
          </div>

          {/* Links Column 1 */}
          <div className="col-span-1 sm:col-span-6 md:col-span-2 space-y-4">
            <h4 className="text-sm font-bold uppercase tracking-wider text-white">Liên kết nhanh</h4>
            <ul className="space-y-2.5 text-sm">
              <li><Link href="/" className="hover:text-primary transition duration-150">Trang chủ</Link></li>
              <li><Link href="/listings" className="hover:text-primary transition duration-150">Nhà đất cho thuê</Link></li>
              <li><Link href="/projects" className="hover:text-primary transition duration-150">Dự án nổi bật</Link></li>
              <li><Link href="/map" className="hover:text-primary transition duration-150">Bản đồ</Link></li>
              <li><Link href="/agents" className="hover:text-primary transition duration-150">Danh sách môi giới</Link></li>
            </ul>
          </div>

          {/* Links Column 2 */}
          <div className="col-span-1 sm:col-span-6 md:col-span-2 space-y-4">
            <h4 className="text-sm font-bold uppercase tracking-wider text-white">Hỗ trợ</h4>
            <ul className="space-y-2.5 text-sm">
              <li><a href="#" className="hover:text-primary transition duration-150">Điều khoản dịch vụ</a></li>
              <li><a href="#" className="hover:text-primary transition duration-150">Chính sách bảo mật</a></li>
              <li><a href="#" className="hover:text-primary transition duration-150">Quy chế hoạt động</a></li>
              <li><a href="#" className="hover:text-primary transition duration-150">Giải quyết khiếu nại</a></li>
              <li><a href="#" className="hover:text-primary transition duration-150">Liên hệ hỗ trợ</a></li>
            </ul>
          </div>

          {/* Newsletter Column */}
          <div className="col-span-1 md:col-span-4 space-y-4">
            <h4 className="text-sm font-bold uppercase tracking-wider text-white">Đăng ký nhận tin tức</h4>
            <p className="text-sm text-slate-400 leading-relaxed">
              Đăng ký nhận thông tin bất động sản mới nhất và các ưu đãi đặc quyền được gửi trực tiếp đến hộp thư của bạn.
            </p>
            <form onSubmit={handleSubscribe} className="space-y-2">
              <div className="relative flex">
                <input 
                  type="email" 
                  placeholder="Địa chỉ Email của bạn..." 
                  required
                  className="w-full bg-slate-900 border border-slate-800 focus:border-primary focus:outline-none rounded-xl px-4.5 py-3 text-sm text-white placeholder-slate-500 transition duration-200"
                />
                <button 
                  type="submit" 
                  className="absolute right-1.5 top-1.5 bottom-1.5 px-4 bg-primary hover:bg-primary-hover text-white text-xs font-bold rounded-lg transition duration-200 shadow-md shadow-primary/20 cursor-pointer"
                >
                  Đăng ký
                </button>
              </div>
            </form>
          </div>
        </div>

        {/* Bottom Copyright */}
        <div className="pt-8 flex flex-col md:flex-row items-center justify-between gap-4 text-xs">
          <div className="text-left space-y-1">
            <p className="font-bold text-slate-355">&copy; {currentYear} BDS Rental - Thương hiệu thuộc Công ty TNHH Giải pháp Công nghệ Tri Thức Mới.</p>
            <p className="text-[11px] leading-relaxed text-slate-500">
              <strong>Tên tiếng Anh:</strong> New Knowledge Technology Solution Ltd. Company<br />
              <strong>Địa chỉ:</strong> 222 Lê Văn Sỹ, Phường 14, Quận 3, TP. Hồ Chí Minh<br />
              <strong>Mã số doanh nghiệp:</strong> 0313074497 (Thành lập ngày 06/01/2015)<br />
              <strong>Hotline:</strong> 0977758217 | <strong>Email:</strong> info@nks.com.vn
            </p>
          </div>
          <div className="flex space-x-6">
            <a href="#" className="hover:text-primary transition duration-150">Sơ đồ trang</a>
            <a href="#" className="hover:text-primary transition duration-150">RSS Feed</a>
          </div>
        </div>
      </div>
    </footer>
  )
}
