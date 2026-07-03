<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Property;
use App\Models\Category;
use App\Models\Appointment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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
        DB::statement('TRUNCATE TABLE property_images CASCADE');
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

        $catDat = Category::create([
            'name' => 'Đất',
            'slug' => 'dat',
            'description' => 'Đất thổ cư, đất nền dự án, đất nông nghiệp cho thuê'
        ]);

        $catKhoNhaXuong = Category::create([
            'name' => 'Kho, nhà xưởng',
            'slug' => 'kho-nha-xuong',
            'description' => 'Kho bãi, nhà xưởng, mặt bằng sản xuất cho thuê'
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

        // 4. Create Owners
        $agentDang = User::create([
            'name' => 'Nguyễn Hải Đăng',
            'email' => 'dang.nguyen@nks.com.vn',
            'phone' => '0987654321',
            'company' => 'Công ty Cổ phần Bất động sản NKS',
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
            'company' => 'Công ty TNHH Địa ốc Đất Xanh',
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
            'company' => 'Tập đoàn Đầu tư Địa ốc Novaland',
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

        // Helper to generate property data array
        $makeProp = function($title, $desc, $price, $priceLabel, $area, $bed, $bath, $addr, $ward, $dist, $city, $lat, $lng, $vip, $new, $catId, $status, $views, $ownerId, $phone, $zalo, $createdAt) {
            return [
                'title' => $title,
                'slug' => Str::slug($title) . '-' . substr(uniqid(), -5),
                'description' => $desc,
                'price' => $price,
                'price_label' => $priceLabel,
                'area' => $area,
                'bedroom' => $bed,
                'bathroom' => $bath,
                'address' => $addr,
                'ward' => $ward,
                'district' => $dist,
                'city' => $city,
                'latitude' => $lat,
                'longitude' => $lng,
                'direction' => 'Đông Nam',
                'furniture' => 'Đầy đủ nội thất, sẵn sàng dọn vào ở',
                'legal' => 'Sổ hồng chính chủ, hợp đồng tối thiểu 1 năm',
                'is_vip' => $vip,
                'is_new' => $new,
                'category_id' => $catId,
                'status' => $status,
                'views_count' => $views,
                'owner_id' => $ownerId,
                'phone' => $phone,
                'zalo' => $zalo,
                'meta_title' => $title,
                'meta_description' => Str::limit(strip_tags($desc), 160),
                'created_at' => $createdAt,
                'updated_at' => $createdAt
            ];
        };

        // 5. Create Properties (Linked to Categories)
        $p1 = Property::create($makeProp(
            'Căn hộ chung cư Vinhomes Ocean Park Studio Full Nội Thất',
            'Căn hộ Studio Vinhomes Ocean Park với thiết kế tối ưu, thoáng đãng, tận dụng tối đa ánh sáng tự nhiên. Căn hộ đã trang bị đầy đủ nội thất cao cấp chỉ việc xách vali vào ở.',
            6500000,
            '6.5tr',
            35,
            1,
            1,
            'Vinhomes Ocean Park, Gia Lâm',
            'Đa Tốn',
            'GL',
            'Hà Nội',
            20.9944,
            105.9567,
            true,
            false,
            $catChungCu->id,
            'approved',
            452,
            $agentDang->id,
            $agentDang->phone,
            'https://zalo.me/' . $agentDang->phone,
            Carbon::now()->subMonths(2)
        ));
        $p1->propertyImages()->create(['image_path' => 'images/apartment_3.png', 'is_primary' => true]);
        $p1->propertyImages()->create(['image_path' => 'images/apartment_1.png', 'is_primary' => false]);
        $p1->propertyImages()->create(['image_path' => 'images/apartment_2.png', 'is_primary' => false]);
        $p1->propertyImages()->create(['image_path' => 'images/hero_bg.png', 'is_primary' => false]);

        $p2 = Property::create($makeProp(
            'Căn hộ Duplex Vinhomes Metropolis Liễu Giai view hồ cực đẹp',
            'Căn hộ thông tầng Duplex độc bản tại Vinhomes Metropolis Liễu Giai, sở hữu tầm nhìn panorama triệu đô hướng trực diện hồ Ngọc Khánh và hồ Tây lộng gió. Căn hộ được thiết kế thông tầng thoáng đãng.',
            18000000,
            '18tr',
            85,
            2,
            2,
            '29 Liễu Giai, Ba Đình',
            'Liễu Giai',
            'BD',
            'Hà Nội',
            21.0315,
            105.8152,
            true,
            true,
            $catChungCu->id,
            'approved',
            295,
            $agentTuyet->id,
            $agentTuyet->phone,
            'https://zalo.me/' . $agentTuyet->phone,
            Carbon::now()->subMonths(1)
        ));
        $p2->propertyImages()->create(['image_path' => 'images/apartment_2.png', 'is_primary' => true]);
        $p2->propertyImages()->create(['image_path' => 'images/apartment_1.png', 'is_primary' => false]);
        $p2->propertyImages()->create(['image_path' => 'images/apartment_3.png', 'is_primary' => false]);
        $p2->propertyImages()->create(['image_path' => 'images/hero_bg.png', 'is_primary' => false]);

        $p3 = Property::create($makeProp(
            'Biệt thự sân vườn Ciputra hiện đại có hồ bơi riêng biệt lập',
            'Biệt thự đơn lập sân vườn tuyệt đẹp tọa lạc tại vị trí đắc địa quận Tây Hồ. Biệt thự có khuôn viên rộng rãi với sân cỏ xanh mướt, hồ bơi riêng biệt ngoài trời cực mát mẻ.',
            45000000,
            '45tr',
            250,
            4,
            4,
            'Khu đô thị Ciputra, Tây Hồ',
            'Phú Thượng',
            'TH',
            'Hà Nội',
            21.0722,
            105.7984,
            true,
            false,
            $catNhaNguyenCan->id,
            'approved',
            610,
            $agentLong->id,
            $agentLong->phone,
            'https://zalo.me/' . $agentLong->phone,
            Carbon::now()->subMonths(2)
        ));
        $p3->propertyImages()->create(['image_path' => 'images/house_2.png', 'is_primary' => true]);
        $p3->propertyImages()->create(['image_path' => 'images/house_1.png', 'is_primary' => false]);
        $p3->propertyImages()->create(['image_path' => 'images/hero_bg.png', 'is_primary' => false]);
        $p3->propertyImages()->create(['image_path' => 'images/apartment_1.png', 'is_primary' => false]);

        $p4 = Property::create($makeProp(
            'Nhà nguyên căn 3 tầng ngõ xe hơi Duy Tân thích hợp làm văn phòng',
            'Nhà nguyên căn mặt tiền ngõ lớn xe hơi tránh nhau tại trung tâm Cầu Giấy. Nhà xây dựng kiên cố 1 trệt 2 lầu sân thượng thoáng mát. Mặt tiền rộng 6m đỗ xe thoải mái.',
            22000000,
            '22tr',
            120,
            3,
            3,
            'Ngõ 86 Duy Tân, Cầu Giấy',
            'Dịch Vọng Hậu',
            'CG',
            'Hà Nội',
            21.0362,
            105.7865,
            false,
            true,
            $catNhaNguyenCan->id,
            'approved',
            180,
            $agentTuan->id,
            $agentTuan->phone,
            'https://zalo.me/' . $agentTuan->phone,
            Carbon::now()->subMonths(1)
        ));
        $p4->propertyImages()->create(['image_path' => 'images/house_1.png', 'is_primary' => true]);
        $p4->propertyImages()->create(['image_path' => 'images/house_2.png', 'is_primary' => false]);
        $p4->propertyImages()->create(['image_path' => 'images/hero_bg.png', 'is_primary' => false]);
        $p4->propertyImages()->create(['image_path' => 'images/apartment_3.png', 'is_primary' => false]);

        $p5 = Property::create($makeProp(
            'Căn hộ chung cư Sky City Láng Hạ nội thất tối giản hiện đại',
            'Căn hộ chung cư 2 phòng ngủ nằm trong tổ hợp chung cư cao cấp Sky City Láng Hạ. Căn hộ được decor theo phong cách Bắc Âu (Scandinavian) tối giản và hiện đại.',
            12000000,
            '12tr',
            72,
            2,
            1,
            '88 Láng Hạ, Đống Đa',
            'Láng Hạ',
            'DD',
            'Hà Nội',
            21.0185,
            105.8159,
            false,
            false,
            $catChungCu->id,
            'approved',
            340,
            $agentMai->id,
            $agentMai->phone,
            'https://zalo.me/' . $agentMai->phone,
            Carbon::now()->subMonths(2)
        ));
        $p5->propertyImages()->create(['image_path' => 'images/apartment_1.png', 'is_primary' => true]);
        $p5->propertyImages()->create(['image_path' => 'images/apartment_2.png', 'is_primary' => false]);
        $p5->propertyImages()->create(['image_path' => 'images/apartment_3.png', 'is_primary' => false]);
        $p5->propertyImages()->create(['image_path' => 'images/hero_bg.png', 'is_primary' => false]);

        $p6 = Property::create($makeProp(
            'Văn phòng hiện đại sẵn bàn ghế làm việc tại trung tâm Hoàn Kiếm',
            'Văn phòng cho thuê cao cấp nằm tại tầng cao trung tâm sầm uất quận Hoàn Kiếm. Không gian văn phòng được thiết kế theo tiêu chuẩn quốc tế, trang bị sẵn đầy đủ hệ thống.',
            35000000,
            '35tr',
            110,
            0,
            2,
            'Tràng Tiền, Hoàn Kiếm',
            'Tràng Tiền',
            'HK',
            'Hà Nội',
            21.0285,
            105.8521,
            false,
            false,
            $catVanPhong->id,
            'approved',
            210,
            $agentDang->id,
            $agentDang->phone,
            'https://zalo.me/' . $agentDang->phone,
            Carbon::now()->subMonths(3)
        ));
        $p6->propertyImages()->create(['image_path' => 'images/apartment_2.png', 'is_primary' => true]);
        $p6->propertyImages()->create(['image_path' => 'images/apartment_3.png', 'is_primary' => false]);
        $p6->propertyImages()->create(['image_path' => 'images/hero_bg.png', 'is_primary' => false]);
        $p6->propertyImages()->create(['image_path' => 'images/house_1.png', 'is_primary' => false]);

        // Approved Land Property
        $p7 = Property::create($makeProp(
            'Cho thuê đất làm kho bãi mặt đường lớn Nam Từ Liêm',
            'Cho thuê diện tích đất thổ cư rộng rãi 500m2 thích hợp làm kho bãi tập kết vật liệu xây dựng, bãi đậu xe, vị trí thuận tiện di chuyển xe container.',
            15000000,
            '15tr',
            500,
            0,
            0,
            'Đường Lê Quang Đạo, Nam Từ Liêm',
            'Mễ Trì',
            'NTL',
            'Hà Nội',
            21.0112,
            105.7725,
            false,
            true,
            $catDat->id,
            'approved',
            142,
            $agentLong->id,
            $agentLong->phone,
            'https://zalo.me/' . $agentLong->phone,
            Carbon::now()->subDays(12)
        ));
        $p7->propertyImages()->create(['image_path' => 'images/house_2.png', 'is_primary' => true]);

        // Approved Warehouse Property
        $p8 = Property::create($makeProp(
            'Cho thuê kho xưởng tiêu chuẩn công nghiệp Hoài Đức',
            'Kho xưởng tiêu chuẩn khung thép zamil, cao 8m, hệ thống pccc cơ bản đầy đủ, điện 3 pha, nước sạch đầy đủ. Mặt đường rộng xe container ra vào dễ dàng.',
            35000000,
            '35tr',
            1000,
            0,
            1,
            'Khu công nghiệp Lai Xá, Hoài Đức',
            'Kim Chung',
            'HD',
            'Hà Nội',
            21.0625,
            105.7285,
            true,
            true,
            $catKhoNhaXuong->id,
            'approved',
            280,
            $agentTuyet->id,
            $agentTuyet->phone,
            'https://zalo.me/' . $agentTuyet->phone,
            Carbon::now()->subDays(8)
        ));
        $p8->propertyImages()->create(['image_path' => 'images/house_1.png', 'is_primary' => true]);

        // Properties awaiting approval (pending)
        $pPending1 = Property::create($makeProp(
            'Phòng trọ khép kín Cầu Giấy giá rẻ cho sinh viên',
            'Cho thuê phòng trọ khép kín diện tích 20m2 ở ngõ 165 Cầu Giấy, an ninh tốt, giờ giấc tự do, không chung chủ, có sẵn internet tốc độ cao.',
            2500000,
            '2.5tr',
            20,
            1,
            1,
            'Ngõ 165 Cầu Giấy',
            'Dịch Vọng',
            'CG',
            'Hà Nội',
            21.0362,
            105.7865,
            false,
            true,
            $catPhongTro->id,
            'pending',
            15,
            $agentMai->id,
            $agentMai->phone,
            'https://zalo.me/' . $agentMai->phone,
            Carbon::now()->subDays(5)
        ));
        $pPending1->propertyImages()->create(['image_path' => 'images/apartment_1.png', 'is_primary' => true]);

        $pPending2 = Property::create($makeProp(
            'Mặt bằng kinh doanh đắc địa quận Đống Đa mặt đường rộng',
            'Mặt bằng cho thuê làm showroom cửa hàng thời trang, mỹ phẩm, tiệm thuốc, văn phòng đại diện trên đường Xã Đàn sầm uất.',
            25000000,
            '25tr',
            60,
            0,
            1,
            'Xã Đàn, Đống Đa',
            'Nam Đồng',
            'DD',
            'Hà Nội',
            21.0185,
            105.8159,
            false,
            true,
            $catMatBang->id,
            'pending',
            24,
            $agentLong->id,
            $agentLong->phone,
            'https://zalo.me/' . $agentLong->phone,
            Carbon::now()->subDays(3)
        ));
        $pPending2->propertyImages()->create(['image_path' => 'images/house_2.png', 'is_primary' => true]);

        // Hidden Properties
        $pHidden = Property::create($makeProp(
            'Căn hộ dịch vụ Studio Đống Đa full đồ tiện nghi',
            'Căn hộ dịch vụ Studio cao cấp thiết kế hiện đại thoáng mát, thang máy, máy giặt dùng chung sân thượng, bảo vệ 24/24.',
            5000000,
            '5tr',
            30,
            1,
            1,
            'Chùa Bộc, Đống Đa',
            'Trung Tự',
            'DD',
            'Hà Nội',
            21.0185,
            105.8159,
            false,
            false,
            $catPhongTro->id,
            'hidden',
            89,
            $agentTuan->id,
            $agentTuan->phone,
            'https://zalo.me/' . $agentTuan->phone,
            Carbon::now()->subMonths(1)
        ));
        $pHidden->propertyImages()->create(['image_path' => 'images/apartment_3.png', 'is_primary' => true]);

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
