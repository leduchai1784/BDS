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
  guide: Article[]
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
    }
  ],
  guide: [
    {
      slug: 'cam-nang-thue-phong-tro-tranh-bay-dat-coc-lua-dao',
      title: 'Cẩm nang thuê phòng trọ: Làm sao để tránh bẫy đặt cọc lừa đảo?',
      image: 'https://images.unsplash.com/photo-1554995207-c18c203602cb?auto=format&fit=crop&q=80&w=800',
      excerpt: 'Hướng dẫn kiểm tra giấy tờ pháp lý chính chủ, biên nhận đặt cọc và quy trình xác minh chủ phòng trọ uy tín.',
      date: '20/06/2026',
      category_label: 'Cẩm nang thuê nhà',
      content: '<h2>1. Cảnh giác trước phòng giá quá rẻ</h2><p class="mb-4">Một chiêu trò phổ biến của kẻ lừa đảo là đăng tin phòng đẹp, vị trí trung tâm nhưng giá rẻ chỉ bằng một nửa thị trường, yêu cầu chuyển khoản đặt cọc giữ chỗ gấp kẻo lỡ.</p><h2>2. Kiểm tra giấy tờ tùy thân của chủ nhà</h2><p class="mb-4">Trước khi giao dịch đặt cọc, bạn luôn có quyền yêu cầu đối chiếu căn cước công dân và sổ hồng/giấy chứng nhận quyền sở hữu của chủ phòng trọ hoặc đại diện ủy quyền hợp pháp.</p>'
    }
  ]
}

export function getAllArticles(): Article[] {
  return [...newsData.report, ...newsData.view, ...newsData.guide]
}

export function getArticleBySlug(slug: string): Article | undefined {
  return getAllArticles().find(a => a.slug === slug)
}
