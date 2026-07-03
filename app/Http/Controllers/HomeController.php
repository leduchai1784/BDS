<?php

namespace App\Http\Controllers;

use App\Services\PropertyService;
use App\Models\Project;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected PropertyService $propertyService;

    public function __construct(PropertyService $propertyService)
    {
        $this->propertyService = $propertyService;
    }

    /**
     * Define synchronized news data for the website
     */
    public static function getNewsData(): array
    {
        return [
            'report' => [
                [
                    'slug' => 'bao-cao-thi-truong-can-ho-cho-thue-tphcm-q2-2026',
                    'title' => 'Báo cáo thị trường căn hộ cho thuê TP.HCM Quý 2/2026',
                    'image' => 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=800&q=80',
                    'excerpt' => 'Thị trường căn hộ dịch vụ và Studio ghi nhận tỷ lệ lấp đầy đạt 85%, giá thuê tăng nhẹ 3-5% tại các khu vực trung tâm Phú Nhuận, Quận 3.',
                    'date' => '28/06/2026',
                    'category_label' => 'Báo cáo thị trường',
                    'content' => '<h2>1. Tổng quan thị trường</h2><p class="mb-4">Trong Quý 2 năm 2026, thị trường căn hộ cho thuê tại khu vực TP.Hồ Chí Minh đã chứng kiến sự hồi phục mạnh mẽ với tỷ lệ lấp đầy trung bình đạt mức 85%. Đặc biệt, phân khúc căn hộ dịch vụ và Studio mini dành cho giới văn phòng và người độc thân tiếp tục ghi nhận nhu cầu cực kỳ lớn.</p><h2>2. Giá thuê trung bình</h2><p class="mb-4">Mức giá thuê căn hộ trung cấp dao động từ 7 - 12 triệu VNĐ/tháng, tăng khoảng 3-5% so với quý trước. Các khu vực trung tâm như Quận 3, Phú Nhuận, Bình Thạnh là tiêu điểm có tỷ suất lấp đầy cao nhất nhờ vị trí đắc địa và hạ tầng đồng bộ.</p><h2>3. Dự báo xu hướng Quý 3</h2><p class="mb-4">Dự báo trong nửa cuối năm, giá thuê sẽ tiếp tục duy trì đà tăng nhẹ do lượng sinh viên nhập học và người đi làm quay lại thành phố tăng cao.</p>'
                ],
                [
                    'slug' => 'xu-huong-dich-chuyen-dong-von-bds-cuoi-nam-2026',
                    'title' => 'Xu hướng dịch chuyển dòng vốn bất động sản nửa cuối năm 2026',
                    'image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=600&q=80',
                    'excerpt' => 'Nhà đầu tư đang ưu tiên các dự án có pháp lý hoàn thiện và có khả năng tạo dòng tiền ngay từ hoạt động cho thuê căn hộ Studio tiện ích.',
                    'date' => '25/06/2026',
                    'category_label' => 'Báo cáo thị trường',
                    'content' => '<h2>1. Khẩu vị nhà đầu tư thay đổi</h2><p class="mb-4">Thị trường bất động sản cuối năm 2026 chứng kiến làn sóng chuyển dịch dòng vốn rõ rệt. Thay vì đầu cơ lướt sóng đất nền vùng ven, các nhà đầu tư cá nhân có xu hướng tập trung dòng tiền vào những dự án căn hộ nội đô có pháp lý hoàn chỉnh và có khả năng đưa vào vận hành khai thác cho thuê ngay lập tức.</p><h2>2. Dòng tiền thông minh hướng về căn hộ dịch vụ</h2><p class="mb-4">Căn hộ Studio tiện ích và căn hộ mini trọn gói ở các khu vực đông dân cư đang mang lại tỷ suất lợi nhuận dòng tiền ổn định từ 8% - 10% mỗi năm, trở thành kênh trú ẩn tài sản an toàn trong bối cảnh lạm phát.</p>'
                ],
                [
                    'slug' => 'bao-cao-tieu-chuan-song-va-lua-chon-can-ho-gioi-tre',
                    'title' => 'Báo cáo tiêu chuẩn sống và xu hướng lựa chọn căn hộ của giới trẻ',
                    'image' => 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=600&q=80',
                    'excerpt' => 'Các căn hộ thông minh tích hợp giải pháp xanh, tiện ích trọn gói và bếp tách riêng biệt đang trở thành ưu tiên số một của nhóm khách hàng trẻ tuổi.',
                    'date' => '18/06/2026',
                    'category_label' => 'Báo cáo thị trường',
                    'content' => '<h2>1. Tiêu chí lựa chọn của thế hệ trẻ</h2><p class="mb-4">Báo cáo khảo sát hành vi người dùng năm 2026 chỉ ra rằng, hơn 75% khách thuê dưới 35 tuổi ưu tiên căn hộ thông minh (Smart Home) có đầy đủ tiện ích như Internet tốc độ cao, hệ thống lọc nước sạch và không gian bếp được phân chia tách biệt khỏi phòng ngủ.</p><h2>2. Đề cao yếu tố môi trường</h2><p class="mb-4">Không gian xanh và ánh sáng tự nhiên cũng đóng vai trò quyết định trong việc ký hợp đồng thuê dài hạn của nhóm khách hàng trẻ tuổi này.</p>'
                ],
                [
                    'slug' => 'cac-yeu-to-anh-huong-den-gia-tri-bds-2026',
                    'title' => 'Các Yếu Tố Ảnh Hưởng Đến Giá Trị Bất Động Sản Năm 2026',
                    'image' => 'https://images.unsplash.com/photo-1460317442991-0ec209397118?auto=format&fit=crop&w=600&q=80',
                    'excerpt' => 'Hạ tầng giao thông, pháp lý dự án và các tiện ích xanh xung quanh là 3 trụ cột cốt lõi quyết định biên độ tăng giá của bất động sản.',
                    'date' => '28/06/2026',
                    'category_label' => 'Báo cáo thị trường',
                    'content' => '<h2>1. Hạ tầng giao thông quyết định giá trị</h2><p class="mb-4">Sự hoàn thiện của các tuyến đường vành đai và tàu điện Metro tiếp tục là đòn bẩy lớn nhất thúc đẩy giá trị bất động sản gia tăng.</p><h2>2. Tiện ích xung quanh và Pháp lý</h2><p class="mb-4">Bên cạnh đó, các dự án sở hữu khuôn viên tiện ích dịch vụ đa dạng và tính pháp lý minh bạch luôn giữ vững biên độ tăng giá tốt bất chấp biến động thị trường.</p>'
                ]
            ],
            'view' => [
                [
                    'slug' => 'goc-nhin-nks-can-ho-studio-quan-7-chiem-linh-phan-khuc-cho-thue',
                    'title' => 'Góc Nhìn NKS: Căn Hộ Studio Quận 7 Đang Dần Chiếm Lĩnh Phân Khúc Cho Thuê',
                    'image' => 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&q=80&w=800',
                    'excerpt' => 'Phân tích xu hướng lựa chọn không gian sống độc lập, tiện ích cao cấp của thế hệ Gen Z và người đi làm độc thân.',
                    'date' => '27/06/2026',
                    'category_label' => 'Góc nhìn NKS'
                ],
                [
                    'slug' => 'goc-nhin-nks-thi-truong-bat-dong-san-cuoi-nam-2026-se-di-ve-dau',
                    'title' => 'Góc nhìn NKS: Thị trường bất động sản cuối năm 2026 sẽ đi về đâu?',
                    'image' => 'https://images.unsplash.com/photo-1582407947304-fd86f028f716?auto=format&fit=crop&q=80&w=600',
                    'excerpt' => 'Phân tích đa chiều về nguồn cung căn hộ dịch vụ và xu hướng giá thuê bất động sản chính chủ.',
                    'date' => '26/06/2026',
                    'category_label' => 'Góc nhìn NKS'
                ],
                [
                    'slug' => 'lam-the-nao-toi-uu-doanh-thu-can-ho-cho-thue',
                    'title' => 'Làm thế nào để tối ưu hóa doanh thu từ căn hộ dịch vụ cho thuê?',
                    'image' => 'https://images.unsplash.com/photo-1556911220-e15b29be8c8f?auto=format&fit=crop&w=600&q=80',
                    'excerpt' => 'Những bài học thực tế từ NKS giúp các chủ đầu tư căn hộ tăng tỷ suất lợi nhuận lên đến 12%/năm nhờ cải tạo thiết kế.',
                    'date' => '29/06/2026',
                    'category_label' => 'Góc nhìn NKS'
                ],
                [
                    'slug' => 'danh-gia-tiem-nang-can-ho-ven-song-sai-gon',
                    'title' => 'Đánh giá tiềm năng tăng trưởng của các căn hộ ven sông Sài Gòn',
                    'image' => 'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?auto=format&fit=crop&w=600&q=80',
                    'excerpt' => 'Tầm nhìn chiến lược về sự phát triển vượt bậc của các căn hộ và khu dân cư dọc trục sông Sài Gòn.',
                    'date' => '20/06/2026',
                    'category_label' => 'Góc nhìn NKS'
                ]
            ],
            'interior' => [
                [
                    'slug' => '5-xu-huong-thiet-ke-noi-that-can-ho-studio-toi-gian-2026',
                    'title' => '5 xu hướng thiết kế nội thất căn hộ Studio tối giản năm 2026',
                    'image' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?auto=format&fit=crop&w=800&q=80',
                    'excerpt' => 'Ứng dụng phong cách tối giản Japandi giúp các không gian căn hộ Studio diện tích nhỏ trở nên thông thoáng, rộng rãi.',
                    'date' => '01/07/2026',
                    'category_label' => 'Nội Thất'
                ],
                [
                    'slug' => 'bi-quyet-lua-chon-vat-lieu-chong-am-moc-toilet-can-ho-dich-vu',
                    'title' => 'Bí quyết lựa chọn vật liệu chống ẩm mốc cho toilet căn hộ dịch vụ',
                    'image' => 'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?auto=format&fit=crop&w=600&q=80',
                    'excerpt' => 'Hướng dẫn chọn gạch men chống thấm, sơn phủ acrylic chống ẩm và thiết kế hệ thống quạt thông gió tối ưu.',
                    'date' => '27/06/2026',
                    'category_label' => 'Nội Thất'
                ],
                [
                    'slug' => 'cach-bai-tri-chieu-sang-giup-khong-gian-am-cung',
                    'title' => 'Cách bài trí hệ thống chiếu sáng giúp không gian sống trở nên ấm cúng',
                    'image' => 'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=600&q=80',
                    'excerpt' => 'Kết hợp hài hòa giữa ánh sáng tự nhiên ban ngày và hệ thống đèn LED âm trần, đèn thả ấm nhiệt độ màu 3000K.',
                    'date' => '15/06/2026',
                    'category_label' => 'Nội Thất'
                ],
                [
                    'slug' => 'bo-tri-sofa-phong-khach-thong-minh-can-ho-nho',
                    'title' => 'Bố trí sofa phòng khách thông minh cho căn hộ nhỏ hẹp',
                    'image' => 'https://images.unsplash.com/photo-1493663284031-b7e3aefcae8e?auto=format&fit=crop&w=600&q=80',
                    'excerpt' => 'Tận dụng góc chết và sử dụng các sản phẩm ghế sofa đa năng, gấp gọn để tối ưu hóa không gian sử dụng.',
                    'date' => '26/6/2026',
                    'category_label' => 'Nội Thất'
                ]
            ],
            'fengshui' => [
                [
                    'slug' => 'phong-thuy-phong-ngu-loi-dai-ky-can-tranh-bo-tri-giuong',
                    'title' => 'Phong thủy phòng ngủ: Những lỗi đại kỵ cần tránh khi bố trí giường',
                    'image' => 'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?auto=format&fit=crop&w=800&q=80',
                    'excerpt' => 'Tránh đặt giường đối diện cửa chính, dưới xà ngang nhà hay trước gương soi lớn nhằm bảo vệ sức khỏe và đón nhận luồng sinh khí.',
                    'date' => '03/07/2026',
                    'category_label' => 'Phong Thủy'
                ],
                [
                    'slug' => 'lua-chon-huong-nha-mau-son-hop-tuoi-menh-tho-2026',
                    'title' => 'Lựa chọn hướng nhà và màu sơn hợp tuổi mệnh Thổ năm Bính Ngọ 2026',
                    'image' => 'https://images.unsplash.com/photo-1513584684374-8bab748fbf90?auto=format&fit=crop&w=600&q=80',
                    'excerpt' => 'Tư vấn chi tiết từ chuyên gia phong thủy giúp gia chủ mệnh Thổ đón vượng khí, tài lộc hanh thông.',
                    'date' => '28/06/2026',
                    'category_label' => 'Phong Thủy'
                ],
                [
                    'slug' => 'bo-tri-cay-xanh-phong-thuy-thu-hut-vuong-khi-phong-khach',
                    'title' => 'Bố trí cây xanh hợp phong thủy giúp thu hút vượng khí cho phòng khách',
                    'image' => 'https://images.unsplash.com/photo-1530018607912-eff2daa1bac4?auto=format&fit=crop&w=600&q=80',
                    'excerpt' => 'Gợi ý các loại cây dễ trồng như Kim Tiền, Thiết Mộc Lan, Vạn Niên Thanh giúp gia tăng năng lượng may mắn.',
                    'date' => '19/06/2026',
                    'category_label' => 'Phong Thủy'
                ],
                [
                    'slug' => 'cach-hoa-giai-guong-doi-dien-cua-phong-ngu',
                    'title' => 'Cách hóa giải gương đối diện cửa phòng ngủ chuẩn phong thủy',
                    'image' => 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=600&q=80',
                    'excerpt' => 'Tác động xấu của gương đối diện giường ngủ/cửa phòng và các biện pháp hóa giải đơn giản như sử dụng rèm che.',
                    'date' => '26/6/2026',
                    'category_label' => 'Phong Thủy'
                ]
            ],
            'news' => [
                [
                    'slug' => 'de-xuat-quy-dinh-moi-quan-ly-van-hanh-chung-cu-mini',
                    'title' => 'Đề xuất quy định mới về quản lý vận hành chung cư mini và nhà trọ',
                    'image' => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=800&q=80',
                    'excerpt' => 'Dự thảo luật mới siết chặt công tác phòng cháy chữa cháy (PCCC) và yêu cầu đăng ký kinh doanh bắt buộc.',
                    'date' => '03/07/2026',
                    'category_label' => 'Tin Tức'
                ],
                [
                    'slug' => 'tphcm-khoi-cong-3-du-an-nha-o-xa-hoi-moi',
                    'title' => 'Thành phố Hồ Chí Minh khởi công xây dựng 3 dự án nhà ở xã hội mới',
                    'image' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=600&q=80',
                    'excerpt' => 'Cung cấp hơn 3,000 căn hộ chất lượng cao giá cả phải chăng dành riêng cho công nhân, người lao động thu nhập thấp.',
                    'date' => '30/06/2026',
                    'category_label' => 'Tin Tức'
                ],
                [
                    'slug' => 'khoi-dong-du-an-cai-tao-ha-tang-giao-thong-truc-duong-chinh',
                    'title' => 'Khởi động dự án cải tạo hạ tầng giao thông trục đường chính',
                    'image' => 'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?auto=format&fit=crop&w=600&q=80',
                    'excerpt' => 'Kế hoạch triển khai nâng cấp mở rộng các tuyến giao thông huyết mạch kết nối trực tiếp với trung tâm thành phố.',
                    'date' => '22/06/2026',
                    'category_label' => 'Tin Tức'
                ],
                [
                    'slug' => 'gia-can-ho-cho-thue-tang-truong-nhe-cuoi-nam',
                    'title' => 'Giá căn hộ cho thuê tiếp tục tăng trưởng nhẹ dịp cuối năm',
                    'image' => 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=600&q=80',
                    'excerpt' => 'Nhu cầu thuê căn hộ chung cư mini và studio tăng cao đột biến trong các tháng cuối năm kéo theo mức giá thuê tăng nhẹ.',
                    'date' => '26/6/2026',
                    'category_label' => 'Tin Tức'
                ]
            ],
            'knowledge' => [
                [
                    'slug' => 'quy-trinh-thu-tuc-chuyen-nhuong-hop-dong-thue-nha',
                    'title' => 'Quy trình và thủ tục chuyển nhượng hợp đồng thuê nhà chuẩn pháp lý',
                    'image' => 'https://images.unsplash.com/photo-1450133064473-71024230f91b?auto=format&fit=crop&w=800&q=80',
                    'excerpt' => 'Hướng dẫn đầy đủ các bước sang nhượng quyền thuê nhà, xử lý phần tiền đặt cọc và lập biên bản thanh lý hợp đồng.',
                    'date' => '02/07/2026',
                    'category_label' => 'Kiến Thức'
                ],
                [
                    'slug' => 'kinh-nghiem-vang-phan-biet-so-hong-that-gia',
                    'title' => 'Kinh nghiệm vàng giúp phân biệt sổ hồng thật và giả khi giao dịch đặt cọc',
                    'image' => 'https://images.unsplash.com/photo-1560520653-9e0e4c89eb11?auto=format&fit=crop&w=600&q=80',
                    'excerpt' => 'Chia sẻ phương pháp kiểm tra phôi sổ hồng bằng mắt thường, xác thực thông tin quy hoạch tránh bẫy lừa đảo.',
                    'date' => '26/06/2026',
                    'category_label' => 'Kiến Thức'
                ],
                [
                    'slug' => 'cac-loai-thue-phi-phai-nop-khi-mua-ban-nha-dat',
                    'title' => 'Các loại thuế phí phải nộp khi mua bán và chuyển nhượng nhà đất',
                    'image' => 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&w=600&q=80',
                    'excerpt' => 'Tổng hợp các loại phí cần đóng gồm thuế thu nhập cá nhân 2%, lệ phí trước bạ 0.5% và cách tính đơn giản chính xác.',
                    'date' => '17/06/2026',
                    'category_label' => 'Kiến Thức'
                ],
                [
                    'slug' => 'kinh-nghiem-quan-ly-tai-chinh-mua-nha-tra-gop-gia-dinh-tre',
                    'title' => 'Kinh nghiệm quản lý tài chính khi mua nhà trả góp cho gia đình trẻ',
                    'image' => 'https://images.unsplash.com/photo-1559526324-4b87b5e36e44?auto=format&fit=crop&w=600&q=80',
                    'excerpt' => 'Lập kế hoạch trả nợ ngân hàng thông minh, áp dụng quy tắc 50/30/20 để quản lý chi tiêu.',
                    'date' => '26/6/2026',
                    'category_label' => 'Kiến Thức'
                ]
            ]
        ];
    }

    /**
     * Display the homepage.
     */
    public function index()
    {
        $featured = $this->propertyService->getFeaturedProperties(8);
        $latest = $this->propertyService->getLatestProperties(4);
        $stats = $this->propertyService->getSystemStats();
        $featuredProjects = Project::latest()->take(3)->get();

        return view('home', [
            'properties' => $featured,
            'latestProperties' => $latest,
            'stats' => $stats,
            'featuredProjects' => $featuredProjects
        ]);
    }

    /**
     * Display the news page.
     */
    public function news()
    {
        return view('news');
    }

    /**
     * Display the news detail page.
     */
    public function newsDetail($slug)
    {
        $allNews = self::getNewsData();
        $foundArticle = null;

        foreach ($allNews as $category => $articles) {
            foreach ($articles as $article) {
                if ($article['slug'] === $slug) {
                    $foundArticle = $article;
                    break 2;
                }
            }
        }

        if (!$foundArticle) {
            abort(404, 'Không tìm thấy bài viết tin tức.');
        }

        return view('news-detail', [
            'article' => $foundArticle
        ]);
    }
}
