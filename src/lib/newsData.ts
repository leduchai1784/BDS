export interface Article {
  slug: string
  title: string
  image: string
  excerpt: string
  date: string
  category_label: string
  content: string
}

export interface NewsData {
  report: Article[]
  view: Article[]
  interior: Article[]
  fengshui: Article[]
  news: Article[]
  knowledge: Article[]
}

export const newsData: NewsData = {
  report: [
    {
      slug: 'bao-cao-thi-truong-can-ho-cho-thue-tphcm-q2-2026',
      title: 'Báo cáo thị trường căn hộ cho thuê TP.HCM Quý 2/2026',
      image: 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=800&q=80',
      excerpt: 'Thị trường căn hộ dịch vụ và Studio ghi nhận tỷ lệ lấp đầy đạt 85%, giá thuê tăng nhẹ 3-5% tại các khu vực trung tâm Phú Nhuận, Quận 3.',
      date: '28/06/2026',
      category_label: 'Báo cáo thị trường',
      content: '<h2>1. Tổng quan thị trường</h2><p class="mb-4">Trong Quý 2 năm 2026, thị trường căn hộ cho thuê tại khu vực TP.Hồ Chí Minh đã chứng kiến sự hồi phục mạnh mẽ với tỷ lệ lấp đầy trung bình đạt mức 85%. Đặc biệt, phân khúc căn hộ dịch vụ và Studio mini dành cho giới văn phòng và người độc thân tiếp tục ghi nhận nhu cầu cực kỳ lớn.</p><h2>2. Giá thuê trung bình</h2><p class="mb-4">Mức giá thuê căn hộ trung cấp dao động từ 7 - 12 triệu VNĐ/tháng, tăng khoảng 3-5% so với quý trước. Các khu vực trung tâm như Quận 3, Phú Nhuận, Bình Thạnh là tiêu điểm có tỷ suất lấp đầy cao nhất nhờ vị trí đắc địa và hạ tầng đồng bộ.</p><h2>3. Dự báo xu hướng Quý 3</h2><p class="mb-4">Dự báo trong nửa cuối năm, giá thuê sẽ tiếp tục duy trì đà tăng nhẹ do lượng sinh viên nhập học và người đi làm quay lại thành phố tăng cao.</p>'
    },
    {
      slug: 'xu-huong-dich-chuyen-dong-von-bds-cuoi-nam-2026',
      title: 'Xu hướng dịch chuyển dòng vốn bất động sản nửa cuối năm 2026',
      image: 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=600&q=80',
      excerpt: 'Nhà đầu tư đang ưu tiên các dự án có pháp lý hoàn thiện và có khả năng tạo dòng tiền ngay từ hoạt động cho thuê căn hộ Studio tiện ích.',
      date: '25/06/2026',
      category_label: 'Báo cáo thị trường',
      content: '<h2>1. Khẩu vị nhà đầu tư thay đổi</h2><p class="mb-4">Thị trường bất động sản cuối năm 2026 chứng kiến làn sóng chuyển dịch dòng vốn rõ rệt. Thay vì đầu cơ lướt sóng đất nền vùng ven, các nhà đầu tư cá nhân có xu hướng tập trung dòng tiền vào những dự án căn hộ nội đô có pháp lý hoàn chỉnh và có khả năng đưa vào vận hành khai thác cho thuê ngay lập tức.</p><h2>2. Dòng tiền thông minh hướng về căn hộ dịch vụ</h2><p class="mb-4">Căn hộ Studio tiện ích và căn hộ mini trọn gói ở các khu vực đông dân cư đang mang lại tỷ suất lợi nhuận dòng tiền ổn định từ 8% - 10% mỗi năm, trở thành kênh trú ẩn tài sản an toàn trong bối cảnh lạm phát.</p>'
    },
    {
      slug: 'bao-cao-tieu-chuan-song-va-lua-chon-can-ho-gioi-tre',
      title: 'Báo cáo tiêu chuẩn sống và xu hướng lựa chọn căn hộ của giới trẻ',
      image: 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=600&q=80',
      excerpt: 'Các căn hộ thông minh tích hợp giải pháp xanh, tiện ích trọn gói và bếp tách riêng biệt đang trở thành ưu tiên số một của nhóm khách hàng trẻ tuổi.',
      date: '18/06/2026',
      category_label: 'Báo cáo thị trường',
      content: '<h2>1. Tiêu chí lựa chọn của thế hệ trẻ</h2><p class="mb-4">Báo cáo khảo sát hành vi người dùng năm 2026 chỉ ra rằng, hơn 75% khách thuê dưới 35 tuổi ưu tiên căn hộ thông minh (Smart Home) có đầy đủ tiện ích như Internet tốc độ cao, hệ thống lọc nước sạch và không gian bếp được phân chia tách biệt khỏi phòng ngủ.</p><h2>2. Đề cao yếu tố môi trường</h2><p class="mb-4">Không gian xanh và ánh sáng tự nhiên cũng đóng vai trò quyết định trong việc ký hợp đồng thuê dài hạn của nhóm khách hàng trẻ tuổi này.</p>'
    },
    {
      slug: 'cac-yeu-to-anh-huong-den-gia-tri-bds-2026',
      title: 'Các Yếu Tố Ảnh Hưởng Đến Giá Trị Bất Động Sản Năm 2026',
      image: 'https://images.unsplash.com/photo-1460317442991-0ec209397118?auto=format&fit=crop&w=600&q=80',
      excerpt: 'Hạ tầng giao thông, pháp lý dự án và các tiện ích xanh xung quanh là 3 trụ cột cốt lõi quyết định biên độ tăng giá của bất động sản.',
      date: '28/06/2026',
      category_label: 'Báo cáo thị trường',
      content: '<h2>1. Hạ tầng giao thông quyết định giá trị</h2><p class="mb-4">Sự hoàn thiện của các tuyến đường vành đai và tàu điện Metro tiếp tục là đòn bẩy lớn nhất thúc đẩy giá trị bất động sản gia tăng.</p><h2>2. Tiện ích xung quanh và Pháp lý</h2><p class="mb-4">Bên cạnh đó, các dự án sở hữu khuôn viên tiện ích dịch vụ đa dạng và tính pháp lý minh bạch luôn giữ vững biên độ tăng giá tốt bất chấp biến động thị trường.</p>'
    }
  ],
  view: [
    {
      slug: 'goc-nhin-nks-can-ho-studio-quan-7-chiem-linh-phan-khuc-cho-thue',
      title: 'Góc Nhìn NKS: Căn Hộ Studio Quận 7 Đang Dần Chiếm Lĩnh Phân Khúc Cho Thuê',
      image: 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&q=80&w=800',
      excerpt: 'Phân tích xu hướng lựa chọn không gian sống độc lập, tiện ích cao cấp của thế hệ Gen Z và người đi làm độc thân.',
      date: '27/06/2026',
      category_label: 'Góc nhìn NKS',
      content: '<h2>1. Nhu cầu bùng nổ tại Quận 7</h2><p class="mb-4">Quận 7 với hạ tầng đồng bộ và lượng lớn trường đại học quốc tế như RMIT, Tôn Đức Thắng đang trở thành điểm nóng của căn hộ Studio cho thuê. Giới trẻ và người nước ngoài độc thân đặc biệt yêu thích mô hình phòng ở tối giản nhưng đầy đủ tiện nghi khép kín tại đây.</p><h2>2. Tỷ suất lợi nhuận lý tưởng</h2><p class="mb-4">Chủ sở hữu căn hộ Studio tại khu vực này ghi nhận mức lợi nhuận cho thuê ổn định nhờ giá thuê tốt và khách hàng có xu hướng ký hợp đồng lâu dài.</p>'
    },
    {
      slug: 'goc-nhin-nks-thi-truong-bat-dong-san-cuoi-nam-2026-se-di-ve-dau',
      title: 'Góc nhìn NKS: Thị trường bất động sản cuối năm 2026 sẽ đi về đâu?',
      image: 'https://images.unsplash.com/photo-1582407947304-fd86f028f716?auto=format&fit=crop&q=80&w=600',
      excerpt: 'Phân tích đa chiều về nguồn cung căn hộ dịch vụ và xu hướng giá thuê bất động sản chính chủ.',
      date: '26/06/2026',
      category_label: 'Góc nhìn NKS',
      content: '<h2>1. Thị trường bước vào giai đoạn phát triển thực chất</h2><p class="mb-4">NKS đánh giá thị trường bất động sản cuối năm 2026 sẽ tập trung mạnh vào các nhu cầu ở thực. Các chủ nhà cung cấp dịch vụ quản lý tốt, phòng ốc sạch sẽ, tiện nghi và giá thuê hợp lý sẽ tiếp tục chiến thắng trên thị trường.</p><h2>2. Sự cạnh tranh về chất lượng dịch vụ</h2><p class="mb-4">Không còn thời kỳ chủ nhà áp đặt giá thuê tùy ý, khách thuê hiện nay thông minh hơn và sẵn sàng so sánh dịch vụ giữa các căn hộ để chọn nơi ở tối ưu nhất.</p>'
    },
    {
      slug: 'lam-the-nao-toi-uu-doanh-thu-can-ho-cho-thue',
      title: 'Làm thế nào để tối ưu hóa doanh thu từ căn hộ dịch vụ cho thuê?',
      image: 'https://images.unsplash.com/photo-1556911220-e15b29be8c8f?auto=format&fit=crop&w=600&q=80',
      excerpt: 'Những bài học thực tế từ NKS giúp các chủ đầu tư căn hộ tăng tỷ suất lợi nhuận lên đến 12%/năm nhờ cải tạo thiết kế.',
      date: '29/06/2026',
      category_label: 'Góc nhìn NKS',
      content: '<h2>1. Cải tạo thiết kế không gian</h2><p class="mb-4">NKS khuyến nghị các chủ nhà nên tập trung tối ưu hóa diện tích sử dụng bằng cách bố trí các sản phẩm nội thất âm tường, giường kéo hoặc vách kính ngăn khu vực bếp.</p><h2>2. Chuyên nghiệp hóa quy trình chăm sóc khách hàng</h2><p class="mb-4">Ứng dụng công nghệ quản lý ra vào bằng khóa vân tay và giải quyết sự cố hư hỏng điện nước trong vòng 24 giờ là chìa khóa giữ chân khách hàng lâu dài.</p>'
    },
    {
      slug: 'danh-gia-tiem-nang-can-ho-ven-song-sai-gon',
      title: 'Đánh giá tiềm năng tăng trưởng của các căn hộ ven sông Sài Gòn',
      image: 'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?auto=format&fit=crop&w=600&q=80',
      excerpt: 'Tầm nhìn chiến lược về sự phát triển vượt bậc của các căn hộ và khu dân cư dọc trục sông Sài Gòn.',
      date: '20/06/2026',
      category_label: 'Góc nhìn NKS',
      content: '<h2>1. Lợi thế cảnh quan sông nước</h2><p class="mb-4">Căn hộ ven sông luôn có giá bán và giá thuê cao hơn khoảng 15-20% so với khu vực lân cận nhờ bầu không khí trong lành và tầm nhìn thoáng đãng.</p><h2>2. Quỹ đất ven sông ngày càng cạn kiệt</h2><p class="mb-4">Sự khan hiếm về quỹ đất ven sông tạo nên giá trị gia tăng bền vững theo thời gian cho các bất động sản sở hữu vị trí đắc địa này.</p>'
    }
  ],
  interior: [
    {
      slug: '5-xu-huong-thiet-ke-noi-that-can-ho-studio-toi-gian-2026',
      title: '5 xu hướng thiết kế nội thất căn hộ Studio tối giản năm 2026',
      image: 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?auto=format&fit=crop&w=800&q=80',
      excerpt: 'Ứng dụng phong cách tối giản Japandi giúp các không gian căn hộ Studio diện tích nhỏ trở nên thông thoáng, rộng rãi.',
      date: '01/07/2026',
      category_label: 'Nội Thất',
      content: '<h2>1. Phong cách Japandi lên ngôi</h2><p class="mb-4">Sự kết hợp hoàn hảo giữa nét mộc mạc của thiết kế Nhật Bản và tính tối giản hiện đại Bắc Âu (Japandi) mang lại cảm giác nhẹ nhàng, thông thoáng.</p><h2>2. Nội thất thông minh đa năng</h2><p class="mb-4">Sử dụng giường ngủ tích hợp hộc chứa đồ, bàn làm việc gấp gọn gắn tường giúp tiết kiệm diện tích tối đa cho căn hộ Studio.</p>'
    },
    {
      slug: 'bi-quyet-lua-chon-vat-lieu-chong-am-moc-toilet-can-ho-dich-vu',
      title: 'Bí quyết lựa chọn vật liệu chống ẩm mốc cho toilet căn hộ dịch vụ',
      image: 'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?auto=format&fit=crop&w=600&q=80',
      excerpt: 'Hướng dẫn chọn gạch men chống thấm, sơn phủ acrylic chống ẩm và thiết kế hệ thống quạt thông gió tối ưu.',
      date: '27/06/2026',
      category_label: 'Nội Thất',
      content: '<h2>1. Sử dụng gạch ceramic kích thước lớn</h2><p class="mb-4">Gạch lớn giúp hạn chế đường ron, nơi dễ tích tụ nước và nấm mốc trong nhà vệ sinh.</p><h2>2. Sơn chống thấm Acrylic và quạt hút công suất lớn</h2><p class="mb-4">Phủ sơn Acrylic chống ẩm mốc cho trần thạch cao và lắp đặt quạt hút thông gió hoạt động êm ái là giải pháp hữu hiệu nhất giữ toilet luôn khô ráo.</p>'
    },
    {
      slug: 'cach-bai-tri-chieu-sang-giup-khong-gian-am-cung',
      title: 'Cách bài trí hệ thống chiếu sáng giúp không gian sống trở nên ấm cúng',
      image: 'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=600&q=80',
      excerpt: 'Kết hợp hài hòa giữa ánh sáng tự nhiên ban ngày và hệ thống đèn LED âm trần, đèn thả ấm nhiệt độ màu 3000K.',
      date: '15/06/2026',
      category_label: 'Nội Thất',
      content: '<h2>1. Nguyên tắc phân lớp ánh sáng</h2><p class="mb-4">Không nên chỉ dùng một nguồn sáng duy nhất từ trần. Hãy kết hợp ánh sáng âm trần nhẹ, đèn thả bàn ăn và đèn cây đọc sách để tạo chiều sâu.</p><h2>2. Nhiệt độ màu ấm 3000K</h2><p class="mb-4">Sử dụng bóng đèn LED ánh sáng vàng ấm nhiệt độ màu khoảng 3000K giúp mắt thư giãn tối đa vào buổi tối.</p>'
    },
    {
      slug: 'bo-tri-sofa-phong-khach-thong-minh-can-ho-nho',
      title: 'Bố trí sofa phòng khách thông minh cho căn hộ nhỏ hẹp',
      image: 'https://images.unsplash.com/photo-1493663284031-b7e3aefcae8e?auto=format&fit=crop&w=600&q=80',
      excerpt: 'Tận dụng góc chết và sử dụng các sản phẩm ghế sofa đa năng, gấp gọn để tối ưu hóa không gian sử dụng.',
      date: '26/06/2026',
      category_label: 'Nội Thất',
      content: '<h2>1. Lựa chọn sofa dáng chữ I gọn gàng</h2><p class="mb-4">Sofa chữ I kê sát tường giúp mở rộng lối đi lại trong căn phòng có diện tích nhỏ hẹp.</p><h2>2. Tận dụng sofa giường (Sofa Bed)</h2><p class="mb-4">Sản phẩm kết hợp giữa ghế tiếp khách ban ngày và giường ngủ ban đêm là lựa chọn tối ưu cho căn hộ Studio.</p>'
    }
  ],
  fengshui: [
    {
      slug: 'phong-thuy-phong-ngu-loi-dai-ky-can-tranh-bo-tri-giuong',
      title: 'Phong thủy phòng ngủ: Những lỗi đại kỵ cần tránh khi bố trí giường',
      image: 'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?auto=format&fit=crop&w=800&q=80',
      excerpt: 'Tránh đặt giường đối diện cửa chính, dưới xà ngang nhà hay trước gương soi lớn nhằm bảo vệ sức khỏe và đón nhận luồng sinh khí.',
      date: '03/07/2026',
      category_label: 'Phong Thủy',
      content: '<h2>1. Tránh kê giường đối diện cửa ra vào</h2><p class="mb-4">Đặt giường thẳng hàng với cửa ra vào tạo thế "quan tài", ảnh hưởng xấu đến sức khỏe và giấc ngủ của gia chủ.</p><h2>2. Tuyệt đối không đặt gương soi chiếu trực tiếp vào giường</h2><p class="mb-4">Gương soi phản chiếu giường ngủ dễ gây bất an, mộng mị và gián đoạn nguồn năng lượng tốt lành trong phòng.</p>'
    },
    {
      slug: 'lua-chon-huong-nha-mau-son-hop-tuoi-menh-tho-2026',
      title: 'Lựa chọn hướng nhà và màu sơn hợp tuổi mệnh Thổ năm Bính Ngọ 2026',
      image: 'https://images.unsplash.com/photo-1513584684374-8bab748fbf90?auto=format&fit=crop&w=600&q=80',
      excerpt: 'Tư vấn chi tiết từ chuyên gia phong thủy giúp gia chủ mệnh Thổ đón vượng khí, tài lộc hanh thông.',
      date: '28/06/2026',
      category_label: 'Phong Thủy',
      content: '<h2>1. Hướng tốt cho mệnh Thổ</h2><p class="mb-4">Người mệnh Thổ nên chọn hướng Đông Bắc hoặc Tây Nam để đón sinh khí và tài lộc thịnh vượng.</p><h2>2. Màu sơn tương sinh tương hợp</h2><p class="mb-4">Sử dụng các gam màu vàng đất, nâu đất (màu bản mệnh) kết hợp hồng, cam, đỏ (màu tương sinh hành Hỏa) để sơn nhà đón may mắn.</p>'
    },
    {
      slug: 'bo-tri-cay-xanh-phong-thuy-thu-hut-vuong-khi-phong-khach',
      title: 'Bố trí cây xanh hợp phong thủy giúp thu hút vượng khí cho phòng khách',
      image: 'https://images.unsplash.com/photo-1530018607912-eff2daa1bac4?auto=format&fit=crop&w=600&q=80',
      excerpt: 'Gợi ý các loại cây dễ trồng như Kim Tiền, Thiết Mộc Lan, Vạn Niên Thanh giúp gia tăng năng lượng may mắn.',
      date: '19/06/2026',
      category_label: 'Phong Thủy',
      content: '<h2>1. Gợi ý các loại cây phong thủy</h2><p class="mb-4">Cây Kim Tiền, Thiết Mộc Lan, Vạn Niên Thanh là những cây mang ý nghĩa chiêu tài đón lộc rất tốt khi đặt ở phòng khách.</p><h2>2. Vị trí đặt cây hợp phong thủy</h2><p class="mb-4">Nên đặt cây ở các góc phòng để hóa giải góc nhọn, hoặc cạnh tivi, cửa ra vào để thanh lọc không khí và điều hòa dòng năng lượng.</p>'
    },
    {
      slug: 'cach-hoa-giai-guong-doi-dien-cua-phong-ngu',
      title: 'Cách hóa giải gương đối diện cửa phòng ngủ chuẩn phong thủy',
      image: 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=600&q=80',
      excerpt: 'Tác động xấu của gương đối diện giường ngủ/cửa phòng và các biện pháp hóa giải đơn giản như sử dụng rèm che.',
      date: '26/06/2026',
      category_label: 'Phong Thủy',
      content: '<h2>1. Tác hại của gương đối diện cửa</h2><p class="mb-4">Thế phạm này đẩy lùi các dòng năng lượng tốt muốn đi vào phòng ngủ, khiến mối quan hệ vợ chồng dễ bất hòa.</p><h2>2. Cách hóa giải đơn giản</h2><p class="mb-4">Chủ nhà có thể dùng rèm vải che gương lại khi không sử dụng, hoặc di dời gương sang vị trí khác khuất tầm mắt.</p>'
    }
  ],
  news: [
    {
      slug: 'de-xuat-quy-dinh-moi-quan-ly-van-hanh-chung-cu-mini',
      title: 'Đề xuất quy định mới về quản lý vận hành chung cư mini và nhà trọ',
      image: 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=800&q=80',
      excerpt: 'Dự thảo luật mới siết chặt công tác phòng cháy chữa cháy (PCCC) và yêu cầu đăng ký kinh doanh bắt buộc.',
      date: '03/07/2026',
      category_label: 'Tin Tức',
      content: '<h2>1. Siết chặt quy định an toàn PCCC</h2><p class="mb-4">Bộ Xây dựng đề xuất dự thảo luật mới yêu cầu tất cả chung cư mini và nhà trọ cho thuê trên 10 phòng phải lắp đặt hệ thống báo cháy tự động và có lối thoát nạn thứ hai.</p><h2>2. Yêu cầu đăng ký kinh doanh chuyên nghiệp</h2><p class="mb-4">Quy định mới cũng hướng tới việc minh bạch hóa nguồn thu và chuẩn hóa chất lượng dịch vụ thuê nhà ở tại các đô thị lớn.</p>'
    },
    {
      slug: 'tphcm-khoi-cong-3-du-an-nha-o-xa-hoi-moi',
      title: 'Thành phố Hồ Chí Minh khởi công xây dựng 3 dự án nhà ở xã hội mới',
      image: 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=600&q=80',
      excerpt: 'Cung cấp hơn 3,000 căn hộ chất lượng cao giá cả phải chăng dành riêng cho công nhân, người lao động thu nhập thấp.',
      date: '30/06/2026',
      category_label: 'Tin Tức',
      content: '<h2>1. Giải quyết nhu cầu nhà ở cho công nhân</h2><p class="mb-4">3 dự án mới khởi công tại Quận 12 và Bình Chánh dự kiến sẽ cung cấp hơn 3,000 căn hộ giá rẻ cho người lao động thu nhập thấp vào cuối năm 2027.</p><h2>2. Hỗ trợ gói vay ưu đãi</h2><p class="mb-4">Người mua nhà tại các dự án này sẽ được tiếp cận gói hỗ trợ tài chính vay mua nhà với lãi suất ưu đãi chỉ từ 4.8%/năm.</p>'
    },
    {
      slug: 'khoi-dong-du-an-cai-tao-ha-tang-giao-thong-truc-duong-chinh',
      title: 'Khởi động dự án cải tạo hạ tầng giao thông trục đường chính',
      image: 'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?auto=format&fit=crop&w=600&q=80',
      excerpt: 'Kế hoạch triển khai nâng cấp mở rộng các tuyến giao thông huyết mạch kết nối trực tiếp với trung tâm thành phố.',
      date: '22/06/2026',
      category_label: 'Tin Tức',
      content: '<h2>1. Nâng cấp các tuyến giao thông huyết mạch</h2><p class="mb-4">Ủy ban nhân dân thành phố chính thức phê duyệt gói thầu cải tạo và nâng cấp trục đường xuyên tâm kết nối các quận ngoại thành với lõi trung tâm đô thị.</p><h2>2. Tác động tích cực đến giá trị bất động sản</h2><p class="mb-4">Dự án hoàn thành hứa hẹn sẽ giảm thiểu 50% thời gian ùn tắc giao thông, tạo làn sóng tăng giá mới cho thị trường căn hộ cho thuê dọc tuyến đường.</p>'
    },
    {
      slug: 'gia-can-ho-cho-thue-tang-truong-nhe-cuoi-nam',
      title: 'Giá căn hộ cho thuê tiếp tục tăng trưởng nhẹ dịp cuối năm',
      image: 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=600&q=80',
      excerpt: 'Nhu cầu thuê căn hộ chung cư mini và studio tăng cao đột biến trong các tháng cuối năm kéo theo mức giá thuê tăng nhẹ.',
      date: '26/06/2026',
      category_label: 'Tin Tức',
      content: '<h2>1. Nhu cầu thuê phòng tăng mạnh</h2><p class="mb-4">Thị trường ghi nhận lượng lớn nhu cầu từ nhóm tân sinh viên nhập học và người đi làm thay đổi nơi ở mới vào quý cuối năm.</p><h2>2. Giá thuê ghi nhận biên độ tăng nhẹ</h2><p class="mb-4">Mức giá thuê căn hộ studio và chung cư mini tăng khoảng 5% - 8% tùy khu vực, đặc biệt sốt giá ở những khu vực gần trường học lớn.</p>'
    }
  ],
  knowledge: [
    {
      slug: 'quy-trinh-thu-tuc-chuyen-nhuong-hop-dong-thue-nha',
      title: 'Quy trình và thủ tục chuyển nhượng hợp đồng thuê nhà chuẩn pháp lý',
      image: 'https://images.unsplash.com/photo-1450133064473-71024230f91b?auto=format&fit=crop&w=800&q=80',
      excerpt: 'Hướng dẫn đầy đủ các bước sang nhượng quyền thuê nhà, xử lý phần tiền đặt cọc và lập biên bản thanh lý hợp đồng.',
      date: '02/07/2026',
      category_label: 'Kiến Thức',
      content: '<h2>1. Các bước sang nhượng quyền thuê nhà</h2><p class="mb-4">Để sang nhượng hợp đồng thuê nhà đúng pháp lý, khách thuê cần làm việc ba bên bao gồm: Chủ nhà, Khách thuê cũ và Khách thuê mới để lập Biên bản thỏa thuận giao dịch sang nhượng.</p><h2>2. Xử lý phần tiền đặt cọc</h2><p class="mb-4">Biên bản cần ghi nhận rõ việc hoàn trả hoặc chuyển giao số tiền đặt cọc cũ để tránh phát sinh tranh chấp tài chính sau này.</p>'
    },
    {
      slug: 'kinh-nghiem-vang-phan-biet-so-hong-that-gia',
      title: 'Kinh nghiệm vàng giúp phân biệt sổ hồng thật và giả khi giao dịch đặt cọc',
      image: 'https://images.unsplash.com/photo-1560520653-9e0e4c89eb11?auto=format&fit=crop&w=600&q=80',
      excerpt: 'Chia sẻ phương pháp kiểm tra phôi sổ hồng bằng mắt thường, xác minh thông tin quy hoạch tránh bẫy lừa đảo.',
      date: '26/06/2026',
      category_label: 'Kiến Thức',
      content: '<h2>1. Kiểm tra phôi sổ hồng dưới ánh sáng</h2><p class="mb-4">Sổ hồng thật có họa tiết in chìm tinh xảo, sắc nét và có mã vạch được in trực tiếp bằng mực chuyên dụng.</p><h2>2. Xác thực thông tin tại cơ quan quản lý</h2><p class="mb-4">Để đảm bảo an toàn tuyệt đối, người mua nên yêu cầu mang sổ hồng đến Văn phòng đăng ký đất đai quận/huyện để tra cứu thông tin quy hoạch trước khi đặt cọc.</p>'
    },
    {
      slug: 'cac-loai-thue-phi-phai-nop-khi-mua-ban-nha-dat',
      title: 'Các loại thuế phí phải nộp khi mua bán và chuyển nhượng nhà đất',
      image: 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&w=600&q=80',
      excerpt: 'Tổng hợp các loại phí cần đóng gồm thuế thu nhập cá nhân 2%, lệ phí trước bạ 0.5% và cách tính đơn giản chính xác.',
      date: '17/06/2026',
      category_label: 'Kiến Thức',
      content: '<h2>1. Thuế thu nhập cá nhân 2%</h2><p class="mb-4">Người bán có nghĩa vụ nộp thuế thu nhập cá nhân bằng 2% trên tổng giá trị giao dịch ghi trên hợp đồng công chứng.</p><h2>2. Lệ phí trước bạ 0.5% và các chi phí khác</h2><p class="mb-4">Người mua nộp lệ phí trước bạ 0.5% cùng lệ phí thẩm định hồ sơ cấp sổ hồng theo quy định hiện hành.</p>'
    },
    {
      slug: 'kinh-nghiem-quan-ly-tai-chinh-mua-nha-tra-gop-gia-dinh-tre',
      title: 'Kinh nghiệm quản lý tài chính khi mua nhà trả góp cho gia đình trẻ',
      image: 'https://images.unsplash.com/photo-1559526324-4b87b5e36e44?auto=format&fit=crop&w=600&q=80',
      excerpt: 'Lập kế hoạch trả nợ ngân hàng thông minh, áp dụng quy tắc 50/30/20 để quản lý chi tiêu.',
      date: '26/06/2026',
      category_label: 'Kiến Thức',
      content: '<h2>1. Quy tắc 30/70 khi vay mua nhà</h2><p class="mb-4">Chỉ nên vay tối đa 50% - 70% giá trị căn nhà, và đảm bảo số tiền trả nợ gốc lãi hàng tháng không vượt quá 30% tổng thu nhập gia đình.</p><h2>2. Chuẩn bị quỹ dự phòng khẩn cấp</h2><p class="mb-4">Luôn duy trì một khoản tiền mặt tương đương 6 tháng chi tiêu sinh hoạt để chủ động tài chính trước các biến động về công việc.</p>'
    }
  ]
}

export function getAllArticles(): Article[] {
  return [
    ...newsData.report, 
    ...newsData.view, 
    ...newsData.interior, 
    ...newsData.fengshui, 
    ...newsData.news, 
    ...newsData.knowledge
  ]
}

export function getArticleBySlug(slug: string): Article | undefined {
  return getAllArticles().find(a => a.slug === slug)
}
