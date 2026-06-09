<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Property;
use App\Models\Category;
use App\Models\Appointment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks for clean seeding
        DB::statement('TRUNCATE TABLE appointments CASCADE');
        DB::statement('TRUNCATE TABLE wishlists CASCADE');
        DB::statement('TRUNCATE TABLE properties CASCADE');
        DB::statement('TRUNCATE TABLE categories CASCADE');
        DB::statement('TRUNCATE TABLE users CASCADE');

        // 1. Create default categories
        $catChungCu = Category::create([
            'name' => 'Chung cư',
            'slug' => 'chung-cu',
            'description' => 'Căn hộ chung cư, căn hộ dịch vụ cao cấp và trung cấp'
        ]);

        $catNhaNguyenCan = Category::create([
            'name' => 'Nhà nguyên căn',
            'slug' => 'nha-nguyen-can',
            'description' => 'Nhà mặt phố, nhà trong ngõ rộng, thích hợp để ở hoặc kinh doanh'
        ]);

        $catPhongTro = Category::create([
            'name' => 'Phòng trọ',
            'slug' => 'phong-tro',
            'description' => 'Phòng trọ bình dân, phòng trọ khép kín cho học sinh sinh viên'
        ]);

        $catVanPhong = Category::create([
            'name' => 'Văn phòng',
            'slug' => 'van-phong',
            'description' => 'Văn phòng cho thuê, không gian làm việc chia sẻ'
        ]);

        $catMatBang = Category::create([
            'name' => 'Mặt bằng',
            'slug' => 'mat-bang',
            'description' => 'Mặt bằng kinh doanh, cửa hàng bán lẻ đường lớn'
        ]);

        // 2. Create Default Renter User (tenant role)
        $renter = User::create([
            'name' => 'Nguyễn Văn Hùng',
            'email' => 'hung.nguyen@nks.com.vn',
            'phone' => '0977758217',
            'password' => Hash::make('password'),
            'role' => 'tenant',
            'status' => 'active',
            'created_at' => Carbon::now()->subMonths(2)
        ]);

        // Create locked tenant for testing
        $lockedRenter = User::create([
            'name' => 'Lê Văn Khóa',
            'email' => 'khoa.le@nks.com.vn',
            'phone' => '0966666666',
            'password' => Hash::make('password'),
            'role' => 'tenant',
            'status' => 'locked',
            'created_at' => Carbon::now()->subMonths(1)
        ]);

        // 3. Create Default Admin User
        $admin = User::create([
            'name' => 'Quản trị viên',
            'email' => 'admin@nks.com.vn',
            'phone' => '0999999999',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
            'created_at' => Carbon::now()->subMonths(3)
        ]);

        // 4. Create Owners (formerly Agents)
        $agentDang = User::create([
            'name' => 'Nguyễn Hải Đăng',
            'email' => 'dang.nguyen@nks.com.vn',
            'phone' => '0987654321',
            'password' => Hash::make('password'),
            'role' => 'owner',
            'status' => 'active',
            'avatar' => 'https://ui-avatars.com/api/?name=Nguyen+Hai+Dang&background=0077bb&color=fff',
            'created_at' => Carbon::now()->subMonths(2)
        ]);

        $agentTuyet = User::create([
            'name' => 'Trần Thị Tuyết',
            'email' => 'tuyet.tran@nks.com.vn',
            'phone' => '0912345678',
            'password' => Hash::make('password'),
            'role' => 'owner',
            'status' => 'active',
            'avatar' => 'https://ui-avatars.com/api/?name=Tran+Thi+Tuyet&background=0077bb&color=fff',
            'created_at' => Carbon::now()->subMonths(2)
        ]);

        $agentLong = User::create([
            'name' => 'Lê Hoàng Long',
            'email' => 'long.le@nks.com.vn',
            'phone' => '0909123456',
            'password' => Hash::make('password'),
            'role' => 'owner',
            'status' => 'active',
            'avatar' => 'https://ui-avatars.com/api/?name=Le+Hoang+Long&background=0077bb&color=fff',
            'created_at' => Carbon::now()->subMonths(1)
        ]);

        $agentTuan = User::create([
            'name' => 'Phạm Minh Tuấn',
            'email' => 'tuan.pham@nks.com.vn',
            'phone' => '0888777999',
            'password' => Hash::make('password'),
            'role' => 'owner',
            'status' => 'active',
            'avatar' => 'https://ui-avatars.com/api/?name=Pham+Minh+Tuan&background=0077bb&color=fff',
            'created_at' => Carbon::now()->subMonths(1)
        ]);

        $agentMai = User::create([
            'name' => 'Hoàng Thanh Mai',
            'email' => 'mai.hoang@nks.com.vn',
            'phone' => '0977888999',
            'password' => Hash::make('password'),
            'role' => 'owner',
            'status' => 'active',
            'avatar' => 'https://ui-avatars.com/api/?name=Hoang+Thanh+Mai&background=0077bb&color=fff',
            'created_at' => Carbon::now()->subMonths(3)
        ]);

        // 5. Create Properties (Linked to Categories)
        $p1 = Property::create([
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
            'category_id' => $catChungCu->id,
            'status' => 'approved',
            'views' => 452,
            'agent_id' => $agentDang->id,
            'description' => 'Căn hộ Studio Vinhomes Ocean Park với thiết kế tối ưu, thoáng đãng, tận dụng tối đa ánh sáng tự nhiên. Căn hộ đã trang bị đầy đủ nội thất cao cấp chỉ việc xách vali vào ở.',
            'created_at' => Carbon::now()->subMonths(2)
        ]);

        $p2 = Property::create([
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
            'category_id' => $catChungCu->id,
            'status' => 'approved',
            'views' => 295,
            'agent_id' => $agentTuyet->id,
            'description' => 'Căn hộ thông tầng Duplex độc bản tại Vinhomes Metropolis Liễu Giai, sở hữu tầm nhìn panorama triệu đô hướng trực diện hồ Ngọc Khánh và hồ Tây lộng gió. Căn hộ được thiết kế thông tầng thoáng đãng.',
            'created_at' => Carbon::now()->subMonths(1)
        ]);

        $p3 = Property::create([
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
            'category_id' => $catNhaNguyenCan->id,
            'status' => 'approved',
            'views' => 610,
            'agent_id' => $agentLong->id,
            'description' => 'Biệt thự đơn lập sân vườn tuyệt đẹp tọa lạc tại vị trí đắc địa quận Tây Hồ. Biệt thự có khuôn viên rộng rãi với sân cỏ xanh mướt, hồ bơi riêng biệt ngoài trời cực mát mẻ.',
            'created_at' => Carbon::now()->subMonths(2)
        ]);

        $p4 = Property::create([
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
            'category_id' => $catNhaNguyenCan->id,
            'status' => 'approved',
            'views' => 180,
            'agent_id' => $agentTuan->id,
            'description' => 'Nhà nguyên căn mặt tiền ngõ lớn xe hơi tránh nhau tại trung tâm Cầu Giấy. Nhà xây dựng kiên cố 1 trệt 2 lầu sân thượng thoáng mát. Mặt tiền rộng 6m đỗ xe thoải mái.',
            'created_at' => Carbon::now()->subMonths(1)
        ]);

        $p5 = Property::create([
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
            'category_id' => $catChungCu->id,
            'status' => 'approved',
            'views' => 340,
            'agent_id' => $agentMai->id,
            'description' => 'Căn hộ chung cư 2 phòng ngủ nằm trong tổ hợp chung cư cao cấp Sky City Láng Hạ. Căn hộ được decor theo phong cách Bắc Âu (Scandinavian) tối giản và hiện đại.',
            'created_at' => Carbon::now()->subMonths(2)
        ]);

        $p6 = Property::create([
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
            'category_id' => $catVanPhong->id,
            'status' => 'approved',
            'views' => 210,
            'agent_id' => $agentDang->id,
            'description' => 'Văn phòng cho thuê cao cấp nằm tại tầng cao trung tâm sầm uất quận Hoàn Kiếm. Không gian văn phòng được thiết kế theo tiêu chuẩn quốc tế, trang bị sẵn đầy đủ hệ thống.',
            'created_at' => Carbon::now()->subMonths(3)
        ]);

        // Properties awaiting approval (pending)
        $pPending1 = Property::create([
            'title' => 'Phòng trọ khép kín Cầu Giấy giá rẻ cho sinh viên',
            'type' => 'Phòng trọ',
            'price' => 2500000,
            'price_label' => '2.5tr',
            'area' => 20,
            'bedrooms' => 1,
            'bathrooms' => 1,
            'location' => 'Cầu Giấy, Hà Nội',
            'district' => 'CG',
            'lat' => 21.0362,
            'lng' => 105.7865,
            'image' => 'images/apartment_1.png',
            'images' => ['images/apartment_1.png'],
            'direction' => 'Đông',
            'furniture' => 'Giường tủ, bình nóng lạnh, kệ bếp nấu ăn',
            'legal' => 'Hợp đồng cọc 1 tháng, đóng tiền 1 tháng',
            'is_vip' => false,
            'is_new' => true,
            'category_id' => $catPhongTro->id,
            'status' => 'pending',
            'views' => 15,
            'agent_id' => $agentMai->id,
            'description' => 'Cho thuê phòng trọ khép kín diện tích 20m2 ở ngõ 165 Cầu Giấy, an ninh tốt, giờ giấc tự do, không chung chủ, có sẵn internet tốc độ cao.',
            'created_at' => Carbon::now()->subDays(5)
        ]);

        $pPending2 = Property::create([
            'title' => 'Mặt bằng kinh doanh đắc địa quận Đống Đa mặt đường rộng',
            'type' => 'Mặt bằng',
            'price' => 25000000,
            'price_label' => '25tr',
            'area' => 60,
            'bedrooms' => 0,
            'bathrooms' => 1,
            'location' => 'Xã Đàn, Đống Đa, Hà Nội',
            'district' => 'CG',
            'lat' => 21.0185,
            'lng' => 105.8159,
            'image' => 'images/house_2.png',
            'images' => ['images/house_2.png'],
            'direction' => 'Nam',
            'furniture' => 'Sàn gạch, cửa kính cường lực sẵn có',
            'legal' => 'Sổ hồng riêng, ký hợp đồng tối thiểu 2 năm',
            'is_vip' => false,
            'is_new' => true,
            'category_id' => $catMatBang->id,
            'status' => 'pending',
            'views' => 24,
            'agent_id' => $agentLong->id,
            'description' => 'Mặt bằng cho thuê làm showroom cửa hàng thời trang, mỹ phẩm, tiệm thuốc, văn phòng đại diện trên đường Xã Đàn sầm uất.',
            'created_at' => Carbon::now()->subDays(3)
        ]);

        // Hidden Properties
        $pHidden = Property::create([
            'title' => 'Căn hộ dịch vụ Studio Đống Đa full đồ tiện nghi',
            'type' => 'Phòng trọ',
            'price' => 5000000,
            'price_label' => '5tr',
            'area' => 30,
            'bedrooms' => 1,
            'bathrooms' => 1,
            'location' => 'Đống Đa, Hà Nội',
            'district' => 'CG',
            'lat' => 21.0185,
            'lng' => 105.8159,
            'image' => 'images/apartment_3.png',
            'images' => ['images/apartment_3.png'],
            'direction' => 'Tây',
            'furniture' => 'Full đồ nội thất sang trọng giường tủ, tivi, sofa, bếp từ',
            'legal' => 'Cọc 1 tháng, thanh toán 1 tháng',
            'is_vip' => false,
            'is_new' => false,
            'category_id' => $catPhongTro->id,
            'status' => 'hidden',
            'views' => 89,
            'agent_id' => $agentTuan->id,
            'description' => 'Căn hộ dịch vụ Studio cao cấp thiết kế hiện đại thoáng mát, thang máy, máy giặt dùng chung sân thượng, bảo vệ 24/24.',
            'created_at' => Carbon::now()->subMonths(1)
        ]);

        // 6. Create viewing appointments
        Appointment::create([
            'user_id' => $renter->id,
            'property_id' => $p1->id,
            'name' => 'Nguyễn Văn Hùng',
            'phone' => '0977758217',
            'date' => Carbon::now()->addDays(2)->format('Y-m-d'),
            'time' => '14:00:00',
            'message' => 'Tôi muốn hẹn đi xem nhà trực tiếp vào chiều nay.',
            'status' => 'confirmed',
            'created_at' => Carbon::now()->subDays(10)
        ]);

        Appointment::create([
            'user_id' => $renter->id,
            'property_id' => $p3->id,
            'name' => 'Nguyễn Văn Hùng',
            'phone' => '0977758217',
            'date' => Carbon::now()->addDays(4)->format('Y-m-d'),
            'time' => '09:30:00',
            'message' => 'Hẹn xem biệt thự Ciputra, xin cảm ơn.',
            'status' => 'pending',
            'created_at' => Carbon::now()->subDays(5)
        ]);

        Appointment::create([
            'user_id' => $renter->id,
            'property_id' => $p5->id,
            'name' => 'Nguyễn Văn Hùng',
            'phone' => '0977758217',
            'date' => Carbon::now()->subDays(1)->format('Y-m-d'),
            'time' => '10:00:00',
            'message' => 'Tôi bận đột xuất nên muốn hẹn ngày khác.',
            'status' => 'cancelled',
            'created_at' => Carbon::now()->subDays(7)
        ]);

        Appointment::create([
            'user_id' => $lockedRenter->id,
            'property_id' => $p2->id,
            'name' => 'Lê Văn Khóa',
            'phone' => '0966666666',
            'date' => Carbon::now()->addDays(1)->format('Y-m-d'),
            'time' => '16:00:00',
            'message' => 'Liên hệ gấp qua số điện thoại này nhé.',
            'status' => 'pending',
            'created_at' => Carbon::now()->subDays(2)
        ]);
    }
}
