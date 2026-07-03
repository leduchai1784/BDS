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
        $tabData = $this->getNewsData();
        return view('news', compact('tabData'));
    }

    /**
     * Display a specific news article.
     */
    public function newsShow($slug)
    {
        $allArticles = [];
        $tabData = $this->getNewsData();
        foreach ($tabData as $cat => $articles) {
            foreach ($articles as $art) {
                $allArticles[$art['slug']] = $art;
            }
        }

        if (!isset($allArticles[$slug])) {
            abort(404, 'Bài viết không tồn tại.');
        }

        $article = $allArticles[$slug];
        
        // Get related articles (same category, excluding current)
        $related = [];
        foreach ($tabData as $cat => $articles) {
            foreach ($articles as $art) {
                if ($art['slug'] !== $slug) {
                    $related[] = $art;
                }
            }
        }
        $related = array_slice($related, 0, 4);

        return view('news-detail', compact('article', 'related'));
    }

    /**
     * Local registry of rich news articles database.
     */
    private function getNewsData()
    {
        return [
            'report' => [
                [
                    'title' => 'Báo cáo thị trường căn hộ cho thuê TP.HCM Quý 2/2026',
                    'slug' => 'bao-cao-thi-truong-can-ho-cho-thue-tphcm-quy-2-2026',
                    'image' => 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=800&q=80',
                    'excerpt' => 'Thị trường căn hộ dịch vụ và Studio ghi nhận tỷ lệ lấp đầy đạt 85%, giá thuê tăng nhẹ 3-5% tại các khu vực trung tâm Phú Nhuận, Quận 3.',
                    'date' => '28/06/2026',
                    'category_label' => 'Báo cáo thị trường',
                    'content' => '<h3>Tình hình chung của thị trường cho thuê</h3><p>Trong quý 2 năm 2026, phân khúc căn hộ dịch vụ và căn hộ studio cho thuê tại khu vực Thành phố Hồ Chí Minh ghi nhận những chuyển biến vô cùng tích cực. Nhờ sự quay trở lại mạnh mẽ của các chuyên gia nước ngoài và lượng sinh viên, người đi làm trẻ tuổi có thu nhập khá, tỷ lệ lấp đầy trung bình toàn thành phố đã chạm mốc 85%.</p><h3>Biến động giá thuê tại các quận trung tâm</h3><p>Mức giá thuê căn hộ trung bình tại các khu vực Phú Nhuận, Quận 3, Quận 1 và Bình Thạnh ghi nhận mức tăng nhẹ từ 3% đến 5% so với quý trước. Cụ thể, các căn hộ studio diện tích từ 30m2 đến 40m2 đầy đủ tiện ích có mức giá thuê dao động từ 7.5 triệu đến 11 triệu đồng/tháng. Phân khúc bình dân hơn ở các khu vực Quận 7 và Tân Bình giữ mức ổn định từ 5 triệu đến 7 triệu đồng/tháng.</p><h3>Dự báo thị trường cuối năm</h3><p>Các chuyên gia NKS dự báo thị trường cho thuê sẽ tiếp tục giữ nhiệt độ ổn định trong hai quý cuối năm 2026. Xu hướng người thuê chú trọng hơn vào các không gian có bếp riêng biệt và thiết kế tối ưu ánh sáng tự nhiên sẽ là đòn bẩy buộc các chủ nhà phải không ngừng cải tạo nâng cấp phòng để duy trì lợi thế cạnh tranh.</p>'
                ],
                [
                    'title' => 'Xu hướng dịch chuyển dòng vốn bất động sản nửa cuối năm 2026',
                    'slug' => 'xu-huong-dich-chuyen-dong-von-bat-dong-san-nua-cuoi-nam-2026',
                    'image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=600&q=80',
                    'excerpt' => 'Nhà đầu tư đang ưu tiên các dự án có pháp lý hoàn thiện và có khả năng tạo dòng tiền ngay từ hoạt động cho thuê căn hộ Studio tiện ích.',
                    'date' => '25/06/2026',
                    'category_label' => 'Báo cáo thị trường',
                    'content' => '<h3>Sự thay đổi khẩu vị của nhà đầu tư</h3><p>Trải qua các giai đoạn biến động, khẩu vị của giới đầu tư bất động sản đã dịch chuyển rõ rệt từ trạng thái kỳ vọng tăng giá vốn nhanh sang ưu tiên bảo toàn vốn và tạo dựng dòng tiền mặt ổn định hàng tháng. Phân khúc bất động sản dòng tiền, tiêu biểu là căn hộ dịch vụ mini và nhà trọ cao cấp, đang trở thành thỏi nam châm thu hút dòng vốn nhàn rỗi.</p><h3>Tiêu chí lựa chọn dự án khắt khe hơn</h3><p>Nhà đầu tư hiện nay không còn dễ dàng đầu cơ theo làn sóng tin đồn hạ tầng. Ba tiêu chí tiên quyết được đặt lên bàn cân bao gồm: Pháp lý hoàn chỉnh (đã có sổ đỏ hoặc giấy phép xây dựng rõ ràng), Vị trí kết nối vùng tốt, và Khả năng đưa vào vận hành khai thác cho thuê ngay lập tức để giảm áp lực lãi vay ngân hàng.</p>'
                ],
                [
                    'title' => 'Báo cáo tiêu chuẩn sống và xu hướng lựa chọn căn hộ của giới trẻ',
                    'slug' => 'bao-cao-tieu-chuan-song-va-xu-huong-lua-chon-can-ho-cua-gioi-tre',
                    'image' => 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=600&q=80',
                    'excerpt' => 'Các căn hộ thông minh tích hợp giải pháp xanh, tiện ích trọn gói và bếp tách riêng biệt đang trở thành ưu tiên số một của nhóm khách hàng trẻ tuổi.',
                    'date' => '18/06/2026',
                    'category_label' => 'Báo cáo thị trường',
                    'content' => '<h3>Phong cách sống mới của thế hệ trẻ</h3><p>Thế hệ trẻ (Gen Y và Gen Z) tại các đô thị lớn đang tái định nghĩa lại tiêu chuẩn về một không gian sống lý tưởng. Không còn đơn thuần là một nơi để ngủ, căn hộ ngày nay phải đáp ứng được các yêu cầu về thẩm mỹ cá nhân, tính tiện nghi công nghệ và đặc biệt là không gian nấu nướng tách biệt để giữ cho phòng ngủ luôn thơm tho.</p><h3>Tiện ích trọn gói lên ngôi</h3><p>Khảo sát thực tế từ BDS Rental cho thấy hơn 78% người trẻ sẵn sàng trả thêm 10-15% chi phí thuê nhà để đổi lấy các gói dịch vụ trọn gói bao gồm: giặt ủi, dọn phòng định kỳ, internet tốc độ cao và hệ thống an ninh 3 lớp điều khiển từ xa qua smartphone. Các căn hộ xanh có ban công nhỏ trồng cây cũng ghi nhận lượt tìm kiếm vượt trội.</p>'
                ],
                [
                    'title' => 'Các Yếu Tố Ảnh Hưởng Đến Giá Trị Bất Động Sản Năm 2026',
                    'slug' => 'cac-yeu-to-anh-huong-den-gia-tri-bat-dong-san-nam-2026',
                    'image' => 'https://images.unsplash.com/photo-1460317442991-0ec209397118?auto=format&fit=crop&w=600&q=80',
                    'excerpt' => 'Hạ tầng giao thông, pháp lý dự án và các tiện ích xanh xung quanh là 3 trụ cột cốt lõi quyết định biên độ tăng giá của bất động sản.',
                    'date' => '28/06/2026',
                    'category_label' => 'Báo cáo thị trường',
                    'content' => '<h3>Ba trụ cột quyết định giá trị bất động sản</h3><p>Bất động sản luôn là tài sản chịu ảnh hưởng đa chiều bởi nhiều yếu tố vĩ mô và vi mô. Trong bối cảnh quy hoạch đô thị mới của năm 2026, ba yếu tố cốt lõi quyết định tính thanh khoản và đà tăng giá của một dự án bao gồm: Sự phát triển hạ tầng giao thông kết nối, Tính minh bạch pháp lý, và Không gian sống xanh an lành.</p><h3>Chi tiết tác động của hạ tầng</h3><p>Các khu vực nằm gần các dự án đường vành đai và các tuyến metro sắp đi vào vận hành ghi nhận tốc độ tăng trưởng giá trị giao dịch cao hơn 15% so với mức trung bình thị trường. Bên cạnh đó, các dự án đạt chứng chỉ kiến trúc xanh hoặc sở hữu công viên sinh thái nội khu cũng định giá cao hơn nhờ đáp ứng nhu cầu sống bảo vệ sức khỏe của cư dân hiện đại.</p>'
                ]
            ],
            'view' => [
                [
                    'title' => 'Góc Nhìn NKS: Căn Hộ Studio Quận 7 Đang Dần Chiếm Lĩnh Phân Khúc Cho Thuê',
                    'slug' => 'goc-nhin-nks-can-ho-studio-quan-7-chiem-linh-phan-khuc-cho-thue',
                    'image' => 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&q=80&w=800',
                    'excerpt' => 'Phân tích xu hướng lựa chọn không gian sống độc lập, tiện ích cao cấp của thế hệ Gen Z và người đi làm độc thân.',
                    'date' => '27/06/2026',
                    'category_label' => 'Góc nhìn NKS',
                    'content' => '<h3>Lý do Quận 7 là mảnh đất vàng của căn hộ Studio</h3><p>Quận 7 từ lâu đã nổi tiếng với quy hoạch bài bản, môi trường sống văn minh đậm chất quốc tế và hệ thống tiện ích đẳng cấp. Với sự tập trung của nhiều trường đại học lớn như RMIT, Tôn Đức Thắng và các tòa nhà văn phòng tại Phú Mỹ Hưng, nhu cầu thuê căn hộ nhỏ gọn từ sinh viên khá giả và nhân sự văn phòng trẻ tại đây là cực kỳ khổng lồ.</p><h3>Ưu thế vượt trội của mô hình Studio tiện ích</h3><p>Thay vì thuê phòng trọ truyền thống chật hẹp hoặc thuê căn hộ 2 phòng ngủ giá quá cao, mô hình căn hộ Studio diện tích từ 28-35m2 với đầy đủ trang thiết bị thông minh trở thành sự lựa chọn tối ưu. Khách thuê chỉ cần mang vali quần áo vào ở, tận hưởng hồ bơi, phòng gym nội khu với mức chi phí vô cùng hợp lý.</p>'
                ],
                [
                    'title' => 'Góc nhìn NKS: Thị trường bất động sản cuối năm 2026 sẽ đi về đâu?',
                    'slug' => 'goc-nhin-nks-thi-truong-bat-dong-san-cuoi-nam-2026-se-di-ve-dau',
                    'image' => 'https://images.unsplash.com/photo-1582407947304-fd86f028f716?auto=format&fit=crop&q=80&w=600',
                    'excerpt' => 'Phân tích đa chiều về nguồn cung căn hộ dịch vụ và xu hướng giá thuê bất động sản chính chủ.',
                    'date' => '26/06/2026',
                    'category_label' => 'Góc nhìn NKS',
                    'content' => '<h3>Những chuyển biến vào dịp cuối năm</h3><p>Thị trường bất động sản vào những tháng cuối năm luôn sôi động do lượng kiều hối đổ về và nhu cầu an cư lạc nghiệp tăng cao. Tuy nhiên, năm 2026 ghi nhận một bức tranh thực tế hơn khi người mua lẫn người thuê đều cực kỳ cẩn trọng, so sánh đối chiếu kỹ lưỡng trước khi đưa ra quyết định đặt bút ký hợp đồng.</p><h3>Nhận định từ các chuyên gia NKS</h3><p>Phân khúc căn hộ dịch vụ chính chủ sẽ tiếp tục chiếm ưu thế lớn nhờ tính ổn định pháp lý và chất lượng quản lý chuyên nghiệp. Giá thuê dự kiến sẽ đi ngang hoặc tăng nhẹ không quá 3%, mở ra cơ hội thương lượng tốt cho các khách thuê thông minh biết nắm bắt thời cơ tìm kiếm những sản phẩm chất lượng cao.</p>'
                ],
                [
                    'title' => 'Làm thế nào để tối ưu hóa doanh thu từ căn hộ dịch vụ cho thuê?',
                    'slug' => 'lam-the-nao-de-toi-uu-hoa-doanh-thu-tu-can-ho-dich-vu-cho-thue',
                    'image' => 'https://images.unsplash.com/photo-1556911220-e15b29be8c8f?auto=format&fit=crop&w=600&q=80',
                    'excerpt' => 'Những bài học thực tế từ NKS giúp các chủ đầu tư căn hộ tăng tỷ suất lợi nhuận lên đến 12%/năm nhờ cải tạo thiết kế.',
                    'date' => '29/06/2026',
                    'category_label' => 'Góc nhìn NKS',
                    'content' => '<h3>Thiết kế thông minh quyết định giá trị</h3><p>Để nâng cao biên độ lợi nhuận từ căn hộ dịch vụ cho thuê, chủ nhà không nhất thiết phải đổ quá nhiều tiền vào nội thất xa xỉ. Điều cốt lõi nằm ở việc phân bổ không gian thông minh. Việc lắp đặt hệ thống giường bục đa năng tích hợp ngăn kéo chứa đồ, kết hợp vách kính lùa ngăn khu vực bếp sẽ giúp căn phòng 25m2 trông rộng rãi như 35m2.</p><h3>Ứng dụng công nghệ quản lý tự động</h3><p>Việc sử dụng khóa vân tay thông minh kết nối wifi, đồng hồ điện nước tử số hóa giúp giảm thiểu 70% chi phí nhân sự quản lý trực tiếp. BDS Rental hỗ trợ các chủ nhà số hóa toàn bộ quy trình tiếp cận khách thuê và quản lý lịch hẹn xem nhà trực tuyến hiệu quả.</p>'
                ],
                [
                    'title' => 'Đánh giá tiềm năng tăng trưởng của các căn hộ ven sông Sài Gòn',
                    'slug' => 'danh-gia-tiem-nang-tang-truong-cua-can-ho-ven-song-sai-gon',
                    'image' => 'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?auto=format&fit=crop&w=600&q=80',
                    'excerpt' => 'Tầm nhìn chiến lược về sự phát triển vượt bậc của các căn hộ và khu dân cư dọc trục sông Sài Gòn.',
                    'date' => '20/06/2026',
                    'category_label' => 'Góc nhìn NKS',
                    'content' => '<h3>Sự đắt giá của tầm nhìn hướng sông</h3><p>Bất động sản ven sông luôn sở hữu mức định giá cao hơn từ 20% đến 35% so với các khu vực lân cận cùng phân khúc. Ngoài lợi thế về cảnh quan thoáng đãng, khí hậu mát mẻ quanh năm, yếu tố phong thủy thịnh vượng "cận lộ, cận giang" cũng là thỏi nam châm thu hút giới thượng lưu và cộng đồng người nước ngoài thuê ở lâu dài.</p><h3>Quy hoạch giao thông đường thủy làm đòn bẩy</h3><p>Sự phát triển của tuyến buýt đường sông (Waterbus) cùng các dự án công viên cảnh quan dọc bờ sông Sài Gòn đang biến các khu căn hộ tại Bình Thạnh, Quận 2 cũ và Thủ Đức trở thành những trung tâm đô thị sinh thái kiểu mẫu mới, hứa hẹn biên độ tăng giá thuê ổn định lâu dài.</p>'
                ]
            ],
            'interior' => [
                [
                    'title' => '5 xu hướng thiết kế nội thất căn hộ Studio tối giản năm 2026',
                    'slug' => '5-xu-huong-thiet-ke-noi-that-can-ho-studio-toi-gian-nam-2026',
                    'image' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?auto=format&fit=crop&w=800&q=80',
                    'excerpt' => 'Ứng dụng phong cách tối giản Japandi giúp các không gian căn hộ Studio diện tích nhỏ trở nên thông thoáng, rộng rãi.',
                    'date' => '01/07/2026',
                    'category_label' => 'Nội Thất',
                    'content' => '<h3>Sự trỗi dậy của phong cách Japandi</h3><p>Phong cách Japandi - sự kết hợp tinh tế giữa nét ấm cúng của phong cách Scandinavian và tính tối giản mộc mạc của Nhật Bản Wabi-Sabi - đang thống trị các xu hướng thiết kế căn hộ nhỏ trong năm 2026. Bằng cách sử dụng tông màu gỗ sồi ấm áp kết hợp tường sơn kem, căn hộ studio của bạn sẽ toát lên vẻ sang trọng cuốn hút.</p><h3>Vật dụng đa chức năng tối ưu không gian</h3><p>Năm xu hướng cụ thể bao gồm: 1. Sofa giường thông minh gấp gọn; 2. Hệ tủ quần áo kịch trần cánh phẳng không tay nắm; 3. Bàn ăn gấp treo tường; 4. Vách ngăn kính cường lực khung sắt mảnh ngăn mùi bếp; 5. Sử dụng cây xanh làm điểm nhấn lọc không khí tự nhiên.</p>'
                ],
                [
                    'title' => 'Bí quyết lựa chọn vật liệu chống ẩm mốc cho toilet căn hộ dịch vụ',
                    'slug' => 'bi-quyet-lua-chon-vat-lieu-chong-am-moc-cho-toilet-can-ho-dich-vu',
                    'image' => 'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?auto=format&fit=crop&w=600&q=80',
                    'excerpt' => 'Hướng dẫn chọn gạch men chống thấm, sơn phủ acrylic chống ẩm và thiết kế hệ thống quạt thông gió tối ưu.',
                    'date' => '27/06/2026',
                    'category_label' => 'Nội Thất',
                    'content' => '<h3>Nỗi lo ẩm mốc nhà vệ sinh căn hộ cho thuê</h3><p>Nhà vệ sinh ẩm ướt, có mùi hôi và nấm mốc đen loang lổ là nguyên nhân hàng đầu khiến khách thuê trả phòng nhanh chóng và đánh giá kém chất lượng căn hộ dịch vụ của bạn. Đầu tư vật liệu chuẩn ngay từ khâu xây dựng thô sẽ giúp bạn tiết kiệm hàng chục triệu đồng chi phí cải tạo bảo dưỡng định kỳ.</p><h3>Giải pháp chọn vật liệu thông minh</h3><p>Hãy ưu tiên sử dụng gạch men bán sứ Porcelian chống thấm nước tuyệt đối, kết hợp keo chà ron gốc epoxy chống rêu mốc. Đối với trần nhà vệ sinh, thay vì dùng thạch cao thông thường, hãy lựa chọn tấm trần nhựa PVC hoặc thạch cao chống ẩm chuyên dụng phủ sơn chống thấm chuyên dụng và lắp đặt quạt thông gió công suất lớn.</p>'
                ],
                [
                    'title' => 'Cách bài trí hệ thống chiếu sáng giúp không gian sống trở nên ấm cúng',
                    'slug' => 'cach-bai-tri-he-thong-chieu-sang-giup-khong-gian-song-tro-nen-am-cung',
                    'image' => 'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=600&q=80',
                    'excerpt' => 'Kết hợp hài hòa giữa ánh sáng tự nhiên ban ngày và hệ thống đèn LED âm trần, đèn thả ấm nhiệt độ màu 3000K.',
                    'date' => '15/06/2026',
                    'category_label' => 'Nội Thất',
                    'content' => '<h3>Nghệ thuật chiếu sáng trong căn hộ nhỏ</h3><p>Ánh sáng quyết định đến 60% cảm xúc và trải nghiệm thư giãn của con người trong một không gian sống. Một căn phòng tràn ngập ánh sáng vàng ấm dịu nhẹ sẽ xua tan đi mọi mệt mỏi của ngày dài làm việc căng thẳng, tạo cảm giác thân thuộc như đang ở một khu resort nghỉ dưỡng sang trọng.</p><h3>Phân chia các lớp ánh sáng hợp lý</h3><p>Hãy phân chia hệ thống đèn thành 3 lớp riêng biệt: Lớp ánh sáng nền (sử dụng đèn LED âm trần tản quang dịu nhẹ màu trung tính 4000K), Lớp ánh sáng chức năng (đèn LED rọi ray tại bếp ăn, đèn đọc sách đầu giường), và Lớp ánh sáng trang trí (đèn thả bàn ăn, dải LED hắt khe trần màu ấm 3000K để tạo chiều sâu).</p>'
                ],
                [
                    'title' => 'Bố trí sofa phòng khách thông minh cho căn hộ nhỏ hẹp',
                    'slug' => 'bo-tri-sofa-phong-khach-thong-minh-cho-can-ho-nho-hep',
                    'image' => 'https://images.unsplash.com/photo-1493663284031-b7e3aefcae8e?auto=format&fit=crop&w=600&q=80',
                    'excerpt' => 'Tận dụng góc chết và sử dụng các sản phẩm ghế sofa đa năng, gấp gọn để tối ưu hóa không gian sử dụng.',
                    'date' => '26/6/2026',
                    'category_label' => 'Nội Thất',
                    'content' => '<h3>Thử thách bố trí nội thất phòng khách nhỏ</h3><p>Phòng khách căn hộ chung cư diện tích nhỏ thường có bề ngang hẹp, gây nhiều khó khăn khi lựa chọn và sắp đặt ghế sofa - món đồ nội thất trung tâm chiếm nhiều diện tích nhất. Bố trí sai cách sẽ khiến luồng giao thông đi lại bị cản trở, tạo cảm giác bức bối chật chội.</p><h3>Mẹo nhỏ từ kiến trúc sư nội thất</h3><p>Hãy lựa chọn mẫu sofa băng chữ I kích thước gọn gàng từ 1.6m đến 1.8m đặt sát tường để giải phóng lối đi. Tránh sử dụng các mẫu sofa chữ L cồng kềnh. Hãy tận dụng góc chết bên hông sofa để đặt một chiếc đèn đứng hoặc cây xanh nhỏ trang trí tạo cảm giác mát mẻ dễ chịu.</p>'
                ]
            ],
            'fengshui' => [
                [
                    'title' => 'Phong thủy phòng ngủ: Những lỗi đại kỵ cần tránh khi bố trí giường ngủ',
                    'slug' => 'phong-thuy-phong-ngu-nhung-loi-dai-ky-can-tranh-khi-bo-tri-giuong-ngu',
                    'image' => 'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?auto=format&fit=crop&w=800&q=80',
                    'excerpt' => 'Tránh đặt giường đối diện cửa chính, dưới xà ngang nhà hay trước gương soi lớn nhằm bảo vệ sức khỏe và đón nhận luồng sinh khí.',
                    'date' => '03/07/2026',
                    'category_label' => 'Phong Thủy',
                    'content' => '<h3>Tầm quan trọng của phong thủy giường ngủ</h3><p>Giường ngủ là nơi con người nghỉ ngơi tái tạo năng lượng suốt 1/3 cuộc đời. Bố trí giường ngủ sai phong thủy không chỉ gây cảm giác bất an, khó ngủ dẫn tới suy nhược cơ thể mà còn ảnh hưởng gián tiếp đến con đường tài lộc, sự nghiệp của gia chủ.</p><h3>Các lỗi đại kỵ cần tuyệt đối tránh</h3><p>1. Đầu giường tựa vào khoảng trống không có tường vững chắc nâng đỡ; 2. Giường ngủ đặt đối diện trực diện với cửa phòng ngủ hoặc cửa nhà vệ sinh; 3. Gương trang điểm lớn phản chiếu thẳng vào giường ngủ gây giật mình hoảng sợ lúc nửa đêm; 4. Đặt đầu giường nằm ngay dưới xà ngang đè nén nặng nề.</p>'
                ],
                [
                    'title' => 'Lựa chọn hướng nhà và màu sơn hợp tuổi mệnh Thổ năm Bính Ngọ 2026',
                    'slug' => 'lua-chon-huong-nha-va-mau-son-hop-tuoi-menh-tho-nam-binh-ngo-2026',
                    'image' => 'https://images.unsplash.com/photo-1513584684374-8bab748fbf90?auto=format&fit=crop&w=600&q=80',
                    'excerpt' => 'Tư vấn chi tiết từ chuyên gia phong thủy giúp gia chủ mệnh Thổ đón vượng khí, tài lộc hanh thông.',
                    'date' => '28/06/2026',
                    'category_label' => 'Phong Thủy',
                    'content' => '<h3>Mệnh Thổ trong năm Bính Ngọ 2026</h3><p>Năm Bính Ngọ 2026 mang năng lượng của hành Hỏa tương sinh cực tốt với người mệnh Thổ. Để khai thác tối đa nguồn sinh khí hanh thông này, việc lựa chọn hướng mua nhà/thuê căn hộ và phối màu sơn trang trí nội thất tương sinh đóng vai trò cực kỳ quan trọng.</p><h3>Hướng nhà và tông màu cát tường</h3><p>Người mệnh Thổ nên chọn hướng nhà Đông Bắc hoặc Tây Nam để đón sinh khí thịnh vượng. Về màu sắc chủ đạo, bên cạnh các gam màu thuộc hành Thổ như vàng đất, nâu đất, hãy tích cực sử dụng các màu sơn hành Hỏa tương sinh như hồng pastel, cam đất, đỏ gạch để trang trí phòng khách tạo không gian ấm cúng sang trọng.</p>'
                ],
                [
                    'title' => 'Bố trí cây xanh hợp phong thủy giúp thu hút vượng khí cho phòng khách',
                    'slug' => 'bo-tri-cay-xanh-hop-phong-thuy-giup-thu-hut-vuong-khi-cho-phong-khach',
                    'image' => 'https://images.unsplash.com/photo-1530018607912-eff2daa1bac4?auto=format&fit=crop&w=600&q=80',
                    'excerpt' => 'Gợi ý các loại cây dễ trồng như Kim Tiền, Thiết Mộc Lan, Vạn Niên Thanh giúp gia tăng năng lượng may mắn.',
                    'date' => '19/06/2026',
                    'category_label' => 'Phong Thủy',
                    'content' => '<h3>Cây xanh - máy lọc sinh khí tự nhiên</h3><p>Bố trí cây xanh trong phòng khách không chỉ giúp cải thiện chất lượng không khí, mang thiên nhiên mát lành vào nhà mà theo thuật phong thủy, cây xanh còn có tác dụng ngăn chặn tà khí và kích hoạt tài lộc nếu được lựa chọn và đặt đúng vị trí sinh tài khí.</p><h3>Gợi ý các loại cây đại cát đại lợi</h3><p>Hãy lựa chọn các loại cây có lá tròn đầy đặn như cây Kim Tiền, Thiết Mộc Lan (Phát Tài), Vạn Niên Thanh đặt tại góc Đông Nam (cung Tài Lộc) hoặc góc phía Đông của phòng khách. Tránh trồng những loại cây có gai sắc nhọn như xương rồng vì dễ sinh sát khí cản trở sự nghiệp.</p>'
                ],
                [
                    'title' => 'Cách hóa giải gương đối diện cửa phòng ngủ chuẩn phong thủy',
                    'slug' => 'cach-hoa-giai-guong-doi-dien-cua-phong-ngu-chuan-phong-thuy',
                    'image' => 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=600&q=80',
                    'excerpt' => 'Tác động xấu của gương đối diện giường ngủ/cửa phòng và các biện pháp hóa giải đơn giản như sử dụng rèm che.',
                    'date' => '26/6/2026',
                    'category_label' => 'Phong Thủy',
                    'content' => '<h3>Tác hại của gương đối diện cửa phòng</h3><p>Theo quan niệm phong thủy cổ xưa, gương là vật phẩm có khả năng phản xạ năng lượng mạnh mẽ. Việc đặt gương soi lớn đối diện cửa phòng ngủ sẽ phản xạ toàn bộ luồng khí tốt vừa đi vào phòng quay trở lại ra ngoài, khiến phòng ngủ tích tụ khí xấu dễ gây mệt mỏi, bất hòa gia đình.</p><h3>Mẹo hóa giải đơn giản hiệu quả</h3><p>Phương pháp tối ưu nhất là di chuyển gương sang vị trí khác bên hông giường ngủ. Trong trường hợp gương gắn tủ cố định không thể di dời, bạn hãy dùng một chiếc rèm vải nhỏ xinh xắn che gương lại khi không sử dụng hoặc dán một lớp decal mờ lên bề mặt gương để triệt tiêu phản xạ sát khí.</p>'
                ]
            ],
            'tintuc' => [
                [
                    'title' => 'Đề xuất quy định mới về quản lý vận hành chung cư mini và nhà trọ',
                    'slug' => 'de-xuat-quy-dinh-moi-ve-quan-ly-van-hanh-chung-cu-mini-va-nha-tro',
                    'image' => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=800&q=80',
                    'excerpt' => 'Dự thảo luật mới siết chặt công tác phòng cháy chữa cháy (PCCC) và yêu cầu đăng ký kinh doanh bắt buộc.',
                    'date' => '03/07/2026',
                    'category_label' => 'Tin Tức',
                    'content' => '<h3>Siết chặt tiêu chuẩn PCCC chung cư mini</h3><p>Trước thực trạng phát triển nóng của các tòa nhà chung cư mini tự phát, Bộ Xây dựng đã đưa ra dự thảo luật mới nhằm siết chặt tiêu chuẩn an toàn PCCC. Theo đó, các tòa nhà cho thuê cao từ 5 tầng trở lên bắt buộc phải trang bị hệ thống báo cháy tự động và có lối thoát hiểm thứ hai rõ ràng độc lập.</p><h3>Yêu cầu bắt buộc đăng ký kinh doanh</h3><p>Các hộ gia đình kinh doanh dịch vụ cho thuê nhà trọ quy mô trên 10 phòng cũng sẽ phải đăng ký mã số thuế kinh doanh hộ cá thể và chịu sự quản lý giám sát định kỳ của chính quyền địa phương nhằm đảm bảo an ninh trật tự khu vực sống.</p>'
                ],
                [
                    'title' => 'Thành phố Hồ Chí Minh khởi công xây dựng 3 dự án nhà ở xã hội mới',
                    'slug' => 'thanh-pho-ho-chi-minh-khoi-cong-xay-dung-3-du-an-nha-o-xa-hoi-moi',
                    'image' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=600&q=80',
                    'excerpt' => 'Cung cấp hơn 3,000 căn hộ chất lượng cao giá cả phải chăng dành riêng cho công nhân, người lao động thu nhập thấp.',
                    'date' => '30/06/2026',
                    'category_label' => 'Tin Tức',
                    'content' => '<h3>Lời giải cho bài toán nhà ở bình dân</h3><p>UBND TP.HCM phối hợp cùng các nhà đầu tư lớn đã chính thức làm lễ động thổ khởi công 3 dự án nhà ở xã hội quy mô lớn tại Quận Bình Tân và Huyện Bình Chánh. Tổng số lượng căn hộ cung ứng ra thị trường dự kiến đạt hơn 3,000 căn vào cuối năm 2027.</p><h3>Đối tượng và chính sách hỗ trợ vay mua nhà</h3><p>Các căn hộ có diện tích từ 45m2 đến 60m2 sẽ được ưu tiên bán cho công nhân làm việc tại các khu chế xuất, công chức nhà nước chưa sở hữu nhà riêng với mức lãi suất ưu đãi cố định 4.8%/năm thông qua Ngân hàng Chính sách Xã hội.</p>'
                ],
                [
                    'title' => 'Khởi động dự án cải tạo hạ tầng giao thông trục đường chính',
                    'slug' => 'khoi-dong-du-an-cai-tao-ha-tang-giao-thong-truc-duong-chinh',
                    'image' => 'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?auto=format&fit=crop&w=600&q=80',
                    'excerpt' => 'Kế hoạch triển khai nâng cấp mở rộng các tuyến giao thông huyết mạch kết nối trực tiếp với trung tâm thành phố.',
                    'date' => '22/06/2026',
                    'category_label' => 'Tin Tức',
                    'content' => '<h3>Mở rộng lộ giới giao thông huyết mạch</h3><p>Sở Giao thông Vận tải đã chính thức phê duyệt phương án đền bù giải phóng mặt bằng để nâng cấp mở rộng lộ giới trục đường chính kết nối các quận vùng ven với lõi trung tâm đô thị. Dự án dự kiến thi công trong vòng 18 tháng với tổng số vốn đầu tư hàng ngàn tỷ đồng.</p><h3>Cú hích mạnh mẽ cho giá trị bất động sản lân cận</h3><p>Việc cải tạo hạ tầng này không chỉ giúp xóa bỏ điểm nghẽn ùn tắc giao thông giờ cao điểm mà còn trực tiếp thúc đẩy giá thuê mặt bằng kinh doanh và giá căn hộ dọc hai bên tuyến đường tăng trưởng ổn định dự báo đạt biên độ 10% sau khi hoàn thành dự án giao thông.</p>'
                ],
                [
                    'title' => 'Giá căn hộ cho thuê tiếp tục tăng trưởng nhẹ dịp cuối năm',
                    'slug' => 'gia-can-ho-cho-thue-tiep-tuc-tang-truong-nhe-dip-cuoi-nam',
                    'image' => 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=600&q=80',
                    'excerpt' => 'Nhu cầu thuê căn hộ chung cư mini và studio tăng cao đột biến trong các tháng cuối năm kéo theo mức giá thuê tăng nhẹ.',
                    'date' => '26/6/2026',
                    'category_label' => 'Tin Tức',
                    'content' => '<h3>Nhu cầu thuê nhà tăng mạnh dịp giáp Tết</h3><p>Theo báo cáo thống kê giao dịch từ hệ thống BDS Rental, lượng khách tìm kiếm thuê phòng trọ và chung cư mini tăng vọt 25% trong giai đoạn cuối năm do đây là thời điểm sinh viên tốt nghiệp chuẩn bị đi làm và nhân sự thay đổi địa điểm công tác chuyển đổi chỗ ở mới đón năm mới.</p><h3>Biến động phân khúc căn hộ mini đầy đủ tiện nghi</h3><p>Nhu cầu tập trung cao ở phân khúc căn hộ mini đầy đủ trang thiết bị nội thất cơ bản với mức giá thuê trung bình dao động ổn định ở mức 5.5 triệu đến 8 triệu đồng/tháng. Các chủ nhà có phòng trống đẹp vào thời gian này dễ dàng lấp đầy nhanh chóng mà không cần giảm giá phòng.</p>'
                ]
            ],
            'kienthuc' => [
                [
                    'title' => 'Quy trình và thủ tục chuyển nhượng hợp đồng thuê nhà chuẩn pháp lý',
                    'slug' => 'quy-trinh-va-thu-tuc-chuyen-nhuong-hop-dong-thue-nha-chuan-phap-ly',
                    'image' => 'https://images.unsplash.com/photo-1450133064473-71024230f91b?auto=format&fit=crop&w=800&q=80',
                    'excerpt' => 'Hướng dẫn đầy đủ các bước sang nhượng quyền thuê nhà, xử lý phần tiền đặt cọc và lập biên bản thanh lý hợp đồng.',
                    'date' => '02/07/2026',
                    'category_label' => 'Kiến Thức',
                    'content' => '<h3>Thế nào là chuyển nhượng hợp đồng thuê?</h3><p>Khi bạn đang thuê nhà dài hạn nhưng cần dọn đi trước thời hạn hợp đồng, việc chuyển nhượng lại quyền thuê cho người mới (thế vị) là giải pháp tối ưu giúp bạn lấy lại tiền cọc và không bị phạt vi phạm hợp đồng thuê nhà. Tuy nhiên, quy trình này phải được thực hiện đúng luật.</p><h3>Các bước thực hiện chặt chẽ an toàn</h3><p>Bước 1: Soạn văn bản xin ý kiến đồng ý bằng văn bản của Chủ nhà; Bước 2: Ký biên bản thỏa thuận ba bên (Chủ nhà cũ, Bạn, và Khách thuê mới); Bước 3: Lập biên bản bàn giao hiện trạng tài sản đính kèm hình ảnh chi tiết; Bước 4: Khách thuê mới ký lại hợp đồng mới trực tiếp với Chủ nhà và hoàn lại tiền cọc cho bạn.</p>'
                ],
                [
                    'title' => 'Kinh nghiệm vàng giúp phân biệt sổ hồng thật và giả khi giao dịch đặt cọc',
                    'slug' => 'kinh-nghiem-vang-giup-phan-biet-so-hong-that-va-gia-khi-giao-dich-dat-coc',
                    'image' => 'https://images.unsplash.com/photo-1560520653-9e0e4c89eb11?auto=format&fit=crop&w=600&q=80',
                    'excerpt' => 'Chia sẻ phương pháp kiểm tra phôi sổ hồng bằng mắt thường, xác thực thông tin quy hoạch tránh bẫy lừa đảo.',
                    'date' => '26/06/2026',
                    'category_label' => 'Kiến Thức',
                    'content' => '<h3>Cảnh giác trước thủ đoạn làm giả sổ đỏ tinh vi</h3><p>Tội phạm làm giả sổ hồng bất động sản ngày càng tinh vi với công nghệ in ấn hiện đại khiến ngay cả những người giao dịch lâu năm cũng dễ sập bẫy. Việc ký cọc tiền mặt lớn trên một cuốn sổ giả sẽ khiến bạn mất trắng toàn bộ số tiền tiết kiệm.</p><h3>Cách tự kiểm tra nhanh chóng bằng mắt thường</h3><p>Hãy chú ý các chi tiết sau: 1. Sử dụng kính lúp kiểm tra họa tiết trống đồng in chìm sắc nét không bị nhòe; 2. Sờ tay trực tiếp lên phần dấu nổi của Sở Tài nguyên và Môi trường có độ lồi lõm chân thực; 3. Mang sổ đến văn phòng đăng ký đất đai của quận/huyện để tra cứu thông tin ngăn chặn trước khi giao dịch chuyển tiền.</p>'
                ],
                [
                    'title' => 'Các loại thuế phí phải nộp khi mua bán và chuyển nhượng nhà đất',
                    'slug' => 'cac-loai-thue-phi-phai-nop-khi-mua-ban-va-chuyen-nhuong-nha-dat',
                    'image' => 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&w=600&q=80',
                    'excerpt' => 'Tổng hợp các loại phí cần đóng gồm thuế thu nhập cá nhân 2%, lệ phí trước bạ 0.5% và cách tính đơn giản chính xác.',
                    'date' => '17/06/2026',
                    'category_label' => 'Kiến Thức',
                    'content' => '<h3>Nghĩa vụ tài chính bắt buộc khi giao dịch</h3><p>Khi tiến hành thủ tục mua bán và chuyển nhượng quyền sử dụng đất đai, nhà ở, cả người bán và người mua đều phải thực hiện đầy đủ nghĩa vụ nộp thuế phí vào ngân sách nhà nước theo đúng quy định để được cấp sổ hồng mới đứng tên.</p><h3>Chi tiết các mức thuế phí hiện hành</h3><p>Người bán có trách nhiệm đóng Thuế Thu nhập Cá nhân trị giá 2% trên tổng giá trị giao dịch ghi trên hợp đồng công chứng. Người mua chịu trách nhiệm đóng Lệ phí Trước bạ trị giá 0.5% để tiến hành đăng bộ sang tên. Ngoài ra còn một số khoản phí nhỏ khác như phí thẩm định hồ sơ đất đai và lệ phí địa chính cấp quận.</p>'
                ],
                [
                    'title' => 'Kinh nghiệm quản lý tài chính khi mua nhà trả góp cho gia đình trẻ',
                    'slug' => 'kinh-nghiem-quan-ly-tai-chinh-khi-mua-nha-tra-gop-cho-gia-dinh-tre',
                    'image' => 'https://images.unsplash.com/photo-1559526324-4b87b5e36e44?auto=format&fit=crop&w=600&q=80',
                    'excerpt' => 'Lập kế hoạch trả nợ ngân hàng thông minh, áp dụng quy tắc 50/30/20 để quản lý chi tiêu.',
                    'date' => '26/6/2026',
                    'category_label' => 'Kiến Thức',
                    'content' => '<h3>Giấc mơ mua nhà của các cặp vợ chồng trẻ</h3><p>Mua nhà trả góp qua ngân hàng là giải pháp tài chính tối ưu giúp các gia đình trẻ sớm sở hữu tổ ấm riêng khi chưa tích lũy đủ 100% số tiền mặt. Tuy nhiên, nếu thiếu đi một kế hoạch quản lý chi tiêu dòng tiền thông minh, khoản nợ gốc lãi hàng tháng có thể biến thành áp lực đè nặng lên cuộc sống hôn nhân.</p><h3>Quy tắc tỷ lệ vàng 30/70</h3><p>Các chuyên gia tài chính khuyến nghị chỉ nên mua nhà khi đã có sẵn tối thiểu 30% giá trị căn nhà bằng tiền tự có, tốt nhất là 50%. Khoản tiền trả nợ ngân hàng hàng tháng không được vượt quá 40% tổng thu nhập cố định của cả hai vợ chồng để duy trì quỹ dự phòng rủi ro ốm đau hoặc thay đổi công việc đột xuất.</p>'
                ]
            ]
        ];
    }
}
