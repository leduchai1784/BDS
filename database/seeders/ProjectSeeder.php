<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Property;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign keys check for MySQL/PostgreSQL
        DB::statement('TRUNCATE TABLE projects CASCADE');

        $projects = [
            [
                'title' => 'Vinhomes Grand Park',
                'slug' => 'vinhomes-grand-park',
                'description' => 'Đại đô thị thông minh Vinhomes Grand Park tại Quận 9 được phát triển bởi tập đoàn Vingroup. Dự án tích hợp đầy đủ tiện ích đẳng cấp thế giới như Công viên 36ha, trường học Vinschool, bệnh viện Vinmec và Vincom Mega Mall.',
                'location' => 'Đường Nguyễn Xiển, Phường Long Thạnh Mỹ, Quận 9',
                'city' => 'Hồ Chí Minh',
                'district' => 'Quận 9',
                'price_range' => '38 - 60 triệu/m²',
                'scale' => '271.8 ha, 71 tòa tháp căn hộ',
                'investor' => 'Vingroup',
                'status' => 'selling',
                'images' => [
                    'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?auto=format&fit=crop&w=800&q=80'
                ],
                'latitude' => 10.8402,
                'longitude' => 106.8373
            ],
            [
                'title' => 'iD Junction Long Thành',
                'slug' => 'id-junction-long-thanh',
                'description' => 'Dự án khu đô thị sinh thái iD Junction tại thị trấn Long Thành, Đồng Nai do Tây Hồ Group làm chủ đầu tư. Dự án sở hữu tọa độ vàng ngay nút giao cao tốc TP.HCM - Long Thành - Dầu Giây và chỉ cách sân bay quốc tế Long Thành 10 phút di chuyển.',
                'location' => 'Đường Phạm Văn Đồng, Thị trấn Long Thành',
                'city' => 'Đồng Nai',
                'district' => 'Huyện Long Thành',
                'price_range' => '55 - 75 triệu/m²',
                'scale' => '40.7 ha, 650 căn nhà phố & biệt thự',
                'investor' => 'Tây Hồ Group',
                'status' => 'selling',
                'images' => [
                    'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=800&q=80'
                ],
                'latitude' => 10.7850,
                'longitude' => 106.9450
            ],
            [
                'title' => 'Khu đô thị Sala Quận 2',
                'slug' => 'khu-do-thi-sala-quan-2',
                'description' => 'Khu đô thị Sala được quy hoạch bài bản, hiện đại hàng đầu Việt Nam nằm ngay lõi Thủ Thiêm, Quận 2. Dự án được đầu tư bởi Đại Quang Minh với các phân khu căn hộ cao cấp Sarimi, Sarina và khu biệt thự sinh thái biệt lập.',
                'location' => 'Số 10 Mai Chí Thọ, Phường Thủ Thiêm, Quận 2',
                'city' => 'Hồ Chí Minh',
                'district' => 'Quận 2',
                'price_range' => '90 - 150 triệu/m²',
                'scale' => '128 ha, gồm căn hộ, shophouse & biệt thự',
                'investor' => 'Đại Quang Minh',
                'status' => 'handed_over',
                'images' => [
                    'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=800&q=80'
                ],
                'latitude' => 10.7719,
                'longitude' => 106.7196
            ],
            [
                'title' => 'Dự án Nhà ở Xã hội Long Thành DNA',
                'slug' => 'du-an-nha-o-xa-hoi-long-thanh-dna',
                'description' => 'Dự án nhà ở xã hội quy mô lớn nhất tại trung tâm Long Thành dành cho đối tượng thu nhập thấp và công nhân khu công nghiệp, mang lại giải pháp an cư lạc nghiệp bền vững với chi phí tối ưu nhất.',
                'location' => 'Xã Lộc An, Huyện Long Thành',
                'city' => 'Đồng Nai',
                'district' => 'Huyện Long Thành',
                'price_range' => '15 - 18 triệu/m²',
                'scale' => '5.2 ha, 1200 căn hộ',
                'investor' => 'DNA Group',
                'status' => 'upcoming',
                'images' => [
                    'https://images.unsplash.com/photo-1570129477492-45c003edd2be?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?auto=format&fit=crop&w=800&q=80'
                ],
                'latitude' => 10.7925,
                'longitude' => 106.9602
            ]
        ];

        foreach ($projects as $projData) {
            $project = Project::create($projData);

            // Update some properties to belong to this project depending on locations
            if (Str::contains($project->title, 'Vinhomes')) {
                // Link properties in HCM City to Vinhomes
                Property::where('city', 'like', '%Hồ Chí Minh%')
                    ->limit(2)
                    ->update(['project_id' => $project->id]);
            } elseif (Str::contains($project->title, 'iD Junction') || Str::contains($project->title, 'DNA')) {
                // Link properties in Dong Nai to these projects
                Property::where('city', 'like', '%Đồng Nai%')
                    ->orWhere('province', 'like', '%Đồng Nai%')
                    ->limit(1)
                    ->update(['project_id' => $project->id]);
            }
        }
    }
}
