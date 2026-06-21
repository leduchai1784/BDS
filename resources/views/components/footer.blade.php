<footer id="contact" class="bg-slate-950 text-slate-400 pt-16 pb-8 border-t border-slate-900 z-10 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 md:gap-12 pb-12 border-b border-slate-900">
            <!-- Brand Column -->
            <div class="col-span-1 md:col-span-4 space-y-5">
                <a href="/" class="flex items-center space-x-2">
                    <div class="w-10 h-10 rounded-xl bg-primary flex items-center justify-center text-white shadow-lg shadow-primary/30">
                        <i class="fa-solid fa-house-chimney text-lg"></i>
                    </div>
                    <span class="font-bold text-2xl tracking-tight text-white">
                        BDS<span class="text-primary">Rental</span>
                    </span>
                </a>
                <p class="text-sm text-slate-400 leading-relaxed max-w-sm">
                    BDS Rental là kênh thông tin bất động sản cho thuê trực thuộc Công ty TNHH Giải pháp Công nghệ Tri Thức Mới. Giúp kết nối nhanh chóng, an toàn giữa người thuê và chủ nhà.
                </p>
                <div class="flex space-x-3.5">
                    <a href="#" class="w-9 h-9 rounded-xl bg-slate-900 hover:bg-primary hover:text-white flex items-center justify-center text-sm transition duration-200">
                        <i class="fa-brands fa-facebook-f"></i>
                    </a>
                    <a href="#" class="w-9 h-9 rounded-xl bg-slate-900 hover:bg-primary hover:text-white flex items-center justify-center text-sm transition duration-200">
                        <i class="fa-brands fa-youtube"></i>
                    </a>
                    <a href="#" class="w-9 h-9 rounded-xl bg-slate-900 hover:bg-primary hover:text-white flex items-center justify-center text-sm transition duration-200">
                        <i class="fa-brands fa-tiktok"></i>
                    </a>
                    <a href="#" class="w-9 h-9 rounded-xl bg-slate-900 hover:bg-primary hover:text-white flex items-center justify-center text-sm transition duration-200">
                        <i class="fa-solid fa-envelope"></i>
                    </a>
                </div>
            </div>

            <!-- Links Column 1 -->
            <div class="col-span-1 sm:col-span-6 md:col-span-2 space-y-4">
                <h4 class="text-sm font-bold uppercase tracking-wider text-white">Khám phá</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="/" class="hover:text-primary transition duration-150">Trang chủ</a></li>
                    <li><a href="/listings" class="hover:text-primary transition duration-150">Kho dự án</a></li>
                    <li><a href="/map" class="hover:text-primary transition duration-150">Bản đồ</a></li>
                    <li><a href="#news" class="hover:text-primary transition duration-150">Tin tức</a></li>
                    <li><a href="#contact" class="hover:text-primary transition duration-150">Liên hệ</a></li>
                </ul>
            </div>

            <!-- Links Column 2 -->
            <div class="col-span-1 sm:col-span-6 md:col-span-2 space-y-4">
                <h4 class="text-sm font-bold uppercase tracking-wider text-white">Hỗ trợ</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="#" class="hover:text-primary transition duration-150">Điều khoản dịch vụ</a></li>
                    <li><a href="#" class="hover:text-primary transition duration-150">Chính sách bảo mật</a></li>
                    <li><a href="#" class="hover:text-primary transition duration-150">Quy chế hoạt động</a></li>
                    <li><a href="#" class="hover:text-primary transition duration-150">Giải quyết khiếu nại</a></li>
                    <li><a href="#" class="hover:text-primary transition duration-150">Liên hệ hỗ trợ</a></li>
                </ul>
            </div>

            <!-- Newsletter Column -->
            <div class="col-span-1 md:col-span-4 space-y-4">
                <h4 class="text-sm font-bold uppercase tracking-wider text-white">Đăng ký nhận tin tức</h4>
                <p class="text-sm text-slate-400 leading-relaxed">
                    Đăng ký nhận thông tin bất động sản mới nhất và các ưu đãi đặc quyền được gửi trực tiếp đến hộp thư của bạn.
                </p>
                <form class="space-y-2" x-on:submit.prevent="alert('Cảm ơn bạn đã đăng ký!')">
                    <div class="relative flex">
                        <input 
                            type="email" 
                            placeholder="Địa chỉ Email của bạn..." 
                            required
                            class="w-full bg-slate-900 border border-slate-800 focus:border-primary focus:outline-none rounded-xl px-4.5 py-3 text-sm text-white placeholder-slate-500 transition duration-200"
                        >
                        <button 
                            type="submit" 
                            class="absolute right-1.5 top-1.5 bottom-1.5 px-4 bg-primary hover:bg-primary-hover text-white text-xs font-bold rounded-lg transition duration-200 shadow-md shadow-primary/20 cursor-pointer"
                        >
                            Đăng ký
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bottom Copyright -->
        <div class="pt-8 flex flex-col md:flex-row items-center justify-between gap-4 text-xs">
            <div class="text-left space-y-1">
                <p class="font-bold text-slate-350">&copy; {{ date('Y') }} BDS Rental - Thương hiệu thuộc Công ty TNHH Giải pháp Công nghệ Tri Thức Mới.</p>
                <p class="text-[11px] leading-relaxed text-slate-500">
                    <strong>Tên tiếng Anh:</strong> New Knowledge Technology Solution Ltd. Company<br>
                    <strong>Địa chỉ:</strong> 222 Lê Văn Sỹ, Phường 14, Quận 3, TP. Hồ Chí Minh<br>
                    <strong>Mã số doanh nghiệp:</strong> 0313074497 (Thành lập ngày 06/01/2015)<br>
                    <strong>Hotline:</strong> 0977758217 | <strong>Email:</strong> info@nks.com.vn
                </p>
            </div>
            <div class="flex space-x-6">
                <a href="#" class="hover:text-primary transition duration-150">Sơ đồ trang</a>
                <a href="#" class="hover:text-primary transition duration-150">RSS Feed</a>
            </div>
        </div>
    </div>
</footer>
