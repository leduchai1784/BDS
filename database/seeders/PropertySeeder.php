<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Property;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Default Tenant User (Nguyen Van Hung)
        $renter = User::create([
            'name' => 'Nguyễn Văn Hùng',
            'email' => 'hung.nguyen@nks.com.vn',
            'phone' => '0977758217',
            'password' => Hash::make('password'),
            'role' => 'tenant',
        ]);

        // 2. Create Owners (formerly Agents)
        $agentDang = User::create([
            'name' => 'Nguyễn Hải Đăng',
            'email' => 'dang.nguyen@nks.com.vn',
            'phone' => '0987654321',
            'password' => Hash::make('password'),
            'role' => 'owner',
            'avatar' => 'https://ui-avatars.com/api/?name=Nguyen+Hai+Dang&background=0077bb&color=fff'
        ]);

        $agentTuyet = User::create([
            'name' => 'Trần Thị Tuyết',
            'email' => 'tuyet.tran@nks.com.vn',
            'phone' => '0912345678',
            'password' => Hash::make('password'),
            'role' => 'owner',
            'avatar' => 'https://ui-avatars.com/api/?name=Tran+Thi+Tuyet&background=0077bb&color=fff'
        ]);

        $agentLong = User::create([
            'name' => 'Lê Hoàng Long',
            'email' => 'long.le@nks.com.vn',
            'phone' => '0909123456',
            'password' => Hash::make('password'),
            'role' => 'owner',
            'avatar' => 'https://ui-avatars.com/api/?name=Le+Hoang+Long&background=0077bb&color=fff'
        ]);

        $agentTuan = User::create([
            'name' => 'Phạm Minh Tuấn',
            'email' => 'tuan.pham@nks.com.vn',
            'phone' => '0888777999',
            'password' => Hash::make('password'),
            'role' => 'owner',
            'avatar' => 'https://ui-avatars.com/api/?name=Pham+Minh+Tuan&background=0077bb&color=fff'
        ]);

        $agentMai = User::create([
            'name' => 'Hoàng Thanh Mai',
            'email' => 'mai.hoang@nks.com.vn',
            'phone' => '0977888999',
            'password' => Hash::make('password'),
            'role' => 'owner',
            'avatar' => 'https://ui-avatars.com/api/?name=Hoang+Thanh+Mai&background=0077bb&color=fff'
        ]);

        // 3. Create Properties
        Property::create([
            'title' => 'Căn hộ chung cư Vinhomes Ocean Park Studio Full Nội Thất',
            'type' => 'Căn hộ chung cư',
            'price' => 6500000,
            'price_label' => '6.5tr',
            'area' => 35,
            'bedrooms' => 1,
            'bathrooms' => 1,
            'location' => 'Gia Lâm, Hà Nội',
            'district' => 'GL',
            'lat' => 20.9944,
            'lng' => 105.9567,
            'image' => 'images/apartment_3.png',
            'images' => [
                'images/apartment_3.png',
                'images/apartment_1.png',
                'images/apartment_2.png',
                'images/hero_bg.png'
            ],
            'direction' => 'Đông Nam',
            'furniture' => 'Đầy đủ nội thất (Tivi, Tủ lạnh, Máy giặt, Điều hòa, Sofa, Giường nệm)',
            'legal' => 'Sổ hồng, Hợp đồng cho thuê tối thiểu 1 năm',
            'is_vip' => true,
            'is_new' => false,
            'agent_id' => $agentDang->id,
            'description' => 'Căn hộ Studio Vinhomes Ocean Park với thiết kế tối ưu, thoáng đãng, tận dụng tối đa ánh sáng tự nhiên. Căn hộ đã trang bị đầy đủ nội thất cao cấp chỉ việc xách vali vào ở. \n\nTiện ích nội khu đẳng cấp: Hồ nước mặn Ocean Park, biển hồ nước ngọt cát trắng mịn, bể bơi bốn mùa, sân tennis, cầu lông, khu BBQ ngoài trời. Hệ thống an ninh đa lớp, camera giám sát 24/7. Phù hợp cho người độc thân hoặc cặp vợ chồng trẻ.'
        ]);

        Property::create([
            'title' => 'Căn hộ Duplex Vinhomes Metropolis Liễu Giai view hồ cực đẹp',
            'type' => 'Căn hộ chung cư',
            'price' => 18000000,
            'price_label' => '18tr',
            'area' => 85,
            'bedrooms' => 2,
            'bathrooms' => 2,
            'location' => 'Ba Đình, Hà Nội',
            'district' => 'CG',
            'lat' => 21.0315,
            'lng' => 105.8152,
            'image' => 'images/apartment_2.png',
            'images' => [
                'images/apartment_2.png',
                'images/apartment_1.png',
                'images/apartment_3.png',
                'images/hero_bg.png'
            ],
            'direction' => 'Tây Nam',
            'furniture' => 'Full nội thất sang trọng nhập khẩu Châu Âu',
            'legal' => 'Hợp đồng công chứng, cọc 2 tháng',
            'is_vip' => true,
            'is_new' => true,
            'agent_id' => $agentTuyet->id,
            'description' => 'Căn hộ thông tầng Duplex độc bản tại Vinhomes Metropolis Liễu Giai, sở hữu tầm nhìn panorama triệu đô hướng trực diện hồ Ngọc Khánh và hồ Tây lộng gió. Căn hộ được thiết kế thông tầng thoáng đãng với phòng khách cao 6m, nội thất hiện đại sang trọng.\n\nCư dân được tận hưởng toàn bộ dịch vụ tiêu chuẩn 5 sao chân tòa nhà, trung tâm thương mại Vincom sầm uất. Cọc 2 tháng thanh toán đầu tháng.'
        ]);

        Property::create([
            'title' => 'Biệt thự sân vườn Ciputra hiện đại có hồ bơi riêng biệt lập',
            'type' => 'Biệt thự / Villa',
            'price' => 45000000,
            'price_label' => '45tr',
            'area' => 250,
            'bedrooms' => 4,
            'bathrooms' => 4,
            'location' => 'Tây Hồ, Hà Nội',
            'district' => 'TH',
            'lat' => 21.0722,
            'lng' => 105.7984,
            'image' => 'images/house_2.png',
            'images' => [
                'images/house_2.png',
                'images/house_1.png',
                'images/hero_bg.png',
                'images/apartment_1.png'
            ],
            'direction' => 'Nam',
            'furniture' => 'Nội thất liền tường cao cấp, khách thuê tự trang bị đồ rời',
            'legal' => 'Hợp đồng dài hạn từ 2 năm trở lên',
            'is_vip' => true,
            'is_new' => false,
            'agent_id' => $agentLong->id,
            'description' => 'Biệt thự đơn lập sân vườn tuyệt đẹp tọa lạc tại vị trí đắc địa quận Tây Hồ. Biệt thự có khuôn viên rộng rãi với sân cỏ xanh mướt, hồ bơi riêng biệt ngoài trời cực mát mẻ.\n\nThiết kế 3 tầng gồm 4 phòng ngủ rộng rãi ngập tràn ánh sáng, phòng khách rộng lớn liên thông bếp ăn hiện đại. Khu vực an ninh, yên tĩnh tuyệt đối, dân trí cao, rất thích hợp cho các chuyên gia nước ngoài hoặc gia đình thượng lưu sinh sống.'
        ]);

        Property::create([
            'title' => 'Nhà nguyên căn 3 tầng ngõ xe hơi Duy Tân thích hợp làm văn phòng',
            'type' => 'Nhà nguyên căn',
            'price' => 22000000,
            'price_label' => '22tr',
            'area' => 120,
            'bedrooms' => 3,
            'bathrooms' => 3,
            'location' => 'Cầu Giấy, Hà Nội',
            'district' => 'CG',
            'lat' => 21.0362,
            'lng' => 105.7865,
            'image' => 'images/house_1.png',
            'images' => [
                'images/house_1.png',
                'images/house_2.png',
                'images/hero_bg.png',
                'images/apartment_3.png'
            ],
            'direction' => 'Đông Bắc',
            'furniture' => 'Cơ bản (Thiết bị vệ sinh, hệ thống đèn chiếu sáng, điều hòa các phòng)',
            'legal' => 'Chính chủ cho thuê, hợp đồng lâu dài',
            'is_vip' => false,
            'is_new' => true,
            'agent_id' => $agentTuan->id,
            'description' => 'Nhà nguyên căn mặt tiền ngõ lớn xe hơi tránh nhau tại trung tâm Cầu Giấy. Nhà xây dựng kiên cố 1 trệt 2 lầu sân thượng thoáng mát. Mặt tiền rộng 6m đỗ xe thoải mái.\n\nKhông gian trống sàn rộng rãi, thích hợp làm văn phòng công ty, spa làm đẹp, trung tâm đào tạo hoặc kinh doanh online kết hợp ở gia đình. Vị trí giao thương thuận lợi, di chuyển nhanh sang các tuyến phố lớn Duy Tân, Xuân Thủy, Trần Thái Tông.'
        ]);

        Property::create([
            'title' => 'Căn hộ chung cư Sky City Láng Hạ nội thất tối giản hiện đại',
            'type' => 'Căn hộ chung cư',
            'price' => 12000000,
            'price_label' => '12tr',
            'area' => 72,
            'bedrooms' => 2,
            'bathrooms' => 1,
            'location' => 'Đống Đa, Hà Nội',
            'district' => 'CG',
            'lat' => 21.0185,
            'lng' => 105.8159,
            'image' => 'images/apartment_1.png',
            'images' => [
                'images/apartment_1.png',
                'images/apartment_2.png',
                'images/apartment_3.png',
                'images/hero_bg.png'
            ],
            'direction' => 'Bắc',
            'furniture' => 'Đầy đủ nội thất thông minh tối giản diện tích',
            'legal' => 'Hợp đồng thuê 1 năm, cọc 1 tháng',
            'is_vip' => false,
            'is_new' => false,
            'agent_id' => $agentMai->id,
            'description' => 'Căn hộ chung cư 2 phòng ngủ nằm trong tổ hợp chung cư cao cấp Sky City Láng Hạ. Căn hộ được decor theo phong cách Bắc Âu (Scandinavian) tối giản và hiện đại, mang lại cảm giác cực kỳ thoải mái và dễ chịu sau giờ làm việc căng thẳng.\n\nDịch vụ quản lý tòa nhà chuyên nghiệp, có phòng tập gym, yoga, siêu thị chân tòa nhà. Vị trí trung tâm Đống Đa dễ dàng kết nối đi Ba Đình, Cầu Giấy và Thanh Xuân.'
        ]);

        Property::create([
            'title' => 'Văn phòng hiện đại sẵn bàn ghế làm việc tại trung tâm Hoàn Kiếm',
            'type' => 'Văn phòng cho thuê',
            'price' => 35000000,
            'price_label' => '35tr',
            'area' => 110,
            'bedrooms' => 0,
            'bathrooms' => 2,
            'location' => 'Hoàn Kiếm, Hà Nội',
            'district' => 'CG',
            'lat' => 21.0285,
            'lng' => 105.8521,
            'image' => 'images/apartment_2.png',
            'images' => [
                'images/apartment_2.png',
                'images/apartment_3.png',
                'images/hero_bg.png',
                'images/house_1.png'
            ],
            'direction' => 'Đông',
            'furniture' => 'Bàn ghế làm việc cao cấp, tủ tài liệu, máy chiếu, bảng viết',
            'legal' => 'Hợp đồng xuất hóa đơn đỏ VAT đầy đủ',
            'is_vip' => false,
            'is_new' => false,
            'agent_id' => $agentDang->id,
            'description' => 'Văn phòng cho thuê cao cấp nằm tại tầng cao trung tâm sầm uất quận Hoàn Kiếm. Không gian văn phòng được thiết kế theo tiêu chuẩn quốc tế, trang bị sẵn đầy đủ hệ thống bàn ghế làm việc, tủ hồ sơ, thiết bị phòng họp hiện đại.\n\nGiá thuê đã bao gồm phí quản lý tòa nhà, nước sinh hoạt và dịch vụ dọn dẹp vệ sinh hàng ngày. Thích hợp cho doanh nghiệp start-up, văn phòng đại diện quy mô 15-20 nhân viên.'
        ]);
    }
}
