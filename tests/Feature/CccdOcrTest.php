<?php
 
 namespace Tests\Feature;
 
 use App\Models\User;
 use Illuminate\Foundation\Testing\DatabaseTransactions;
 use Illuminate\Support\Facades\Http;
 use Tests\TestCase;
 
 class CccdOcrTest extends TestCase
 {
     use DatabaseTransactions;
 
     protected User $user;
 
     protected function setUp(): void
     {
         parent::setUp();
 
         $this->user = User::firstOrCreate(
             ['email' => 'tenant.test@nks.com.vn'],
             [
                 'name' => 'Người Thuê Test',
                 'password' => bcrypt('password'),
                 'role' => 'tenant',
             ]
         );
     }
 
     public function test_scan_cccd_requires_authentication(): void
     {
         $response = $this->postJson(route('profile.scan-cccd'), [
             'image' => 'data:image/jpeg;base64,' . base64_encode('fake image'),
             'side' => 'front'
         ]);
 
         $response->assertStatus(401);
     }
 
     public function test_scan_cccd_validation_errors(): void
     {
         $response = $this->actingAs($this->user)->postJson(route('profile.scan-cccd'), [
             'image' => '',
             'side' => 'invalid-side'
         ]);
 
         $response->assertStatus(422);
     }
 
     public function test_scan_cccd_front_success(): void
     {
         Http::fake([
             'https://api.fpt.ai/vision/idr/vnm' => Http::response([
                 'errorCode' => 0,
                 'errorMessage' => '',
                 'data' => [
                     [
                         'id' => '079195012345',
                         'dob' => '24/10/1995',
                         'home' => 'Ba Đình, Hà Nội',
                         'address' => '123 Phố Huế, Hai Bà Trưng, Hà Nội'
                     ]
                 ]
             ], 200)
         ]);
 
         $fakeImage = 'data:image/jpeg;base64,' . base64_encode('fake image data');
 
         $response = $this->actingAs($this->user)->postJson(route('profile.scan-cccd'), [
             'image' => $fakeImage,
             'side' => 'front'
         ]);
 
         $response->assertStatus(200)
             ->assertJson([
                 'success' => true,
                 'data' => [
                     'number' => '079195012345',
                     'dob' => '1995-10-24',
                     'pob' => 'Ba Đình, Hà Nội',
                     'permanent_address' => '123 Phố Huế, Hai Bà Trưng, Hà Nội'
                 ]
             ]);
     }
 
     public function test_scan_cccd_back_success(): void
     {
         Http::fake([
             'https://api.fpt.ai/vision/idr/vnm' => Http::response([
                 'errorCode' => 0,
                 'errorMessage' => '',
                 'data' => [
                     [
                         'issue_date' => '20/10/2022',
                         'issue_loc' => 'CỤC TRƯỞNG CỤC CẢNH SÁT QUẢN LÝ HÀNH CHÍNH VỀ TRẬT TỰ XÃ HỘI',
                         'address' => '123 Phố Huế, Hai Bà Trưng, Hà Nội'
                     ]
                 ]
             ], 200)
         ]);
 
         $fakeImage = 'data:image/jpeg;base64,' . base64_encode('fake image data');
 
         $response = $this->actingAs($this->user)->postJson(route('profile.scan-cccd'), [
             'image' => $fakeImage,
             'side' => 'back'
         ]);
 
         $response->assertStatus(200)
             ->assertJson([
                 'success' => true,
                 'data' => [
                     'issue_date' => '2022-10-20',
                     'issue_place' => 'CỤC TRƯỞNG CỤC CẢNH SÁT QUẢN LÝ HÀNH CHÍNH VỀ TRẬT TỰ XÃ HỘI',
                     'permanent_address' => '123 Phố Huế, Hai Bà Trưng, Hà Nội'
                 ]
             ]);
     }
 
     public function test_scan_cccd_fpt_error_handling(): void
     {
         Http::fake([
             'https://api.fpt.ai/vision/idr/vnm' => Http::response([
                 'errorCode' => 1,
                 'errorMessage' => 'Image is invalid'
             ], 200)
         ]);
 
         $fakeImage = 'data:image/jpeg;base64,' . base64_encode('fake image data');
 
         $response = $this->actingAs($this->user)->postJson(route('profile.scan-cccd'), [
             'image' => $fakeImage,
             'side' => 'front'
         ]);
 
         $response->assertStatus(422)
             ->assertJson([
                 'success' => false,
                 'message' => 'Lỗi OCR từ FPT: Image is invalid'
             ]);
     }
 
     public function test_update_cccd_saves_locally(): void
     {
         $payload = [
             'dob' => '1995-10-24',
             'pob' => 'Ba Đình, Hà Nội',
             'id_number' => '079195012345',
             'id_date' => '2022-10-20',
             'id_place' => 'Cục Cảnh sát QLHC về TTXH',
             'permanent_address' => '123 Phố Huế, Hai Bà Trưng, Hà Nội',
             'cccd_front' => 'data:image/jpeg;base64,' . base64_encode('fake front image'),
             'cccd_back' => 'data:image/jpeg;base64,' . base64_encode('fake back image'),
         ];
 
         $response = $this->actingAs($this->user)
             ->post(route('profile.cccd'), $payload);
 
         $response->assertRedirect(route('profile.index', ['tab' => 'profile', 'subtab' => 'cccd']));
         
         $this->user->refresh();
         $this->assertEquals('079195012345', $this->user->id_number);
         $this->assertEquals('24/10/1995', $this->user->dob);
         $this->assertEquals('20/10/2022', $this->user->id_date);
         $this->assertEquals('Ba Đình, Hà Nội', $this->user->pob);
         $this->assertEquals('Cục Cảnh sát QLHC về TTXH', $this->user->id_place);
         $this->assertEquals('123 Phố Huế, Hai Bà Trưng, Hà Nội', $this->user->permanent_address);
     }
 
     public function test_update_cccd_syncs_to_nks(): void
     {
         $this->user->update(['nks_token' => 'mock-nks-token']);
 
         Http::fake([
             'https://account.nks.vn/api/nks/user/updateCccd' => Http::response([
                 'success' => true,
                 'message' => 'CCCD updated successfully'
             ], 200),
             'https://account.nks.vn/api/nks/user/updateInfo' => Http::response([
                 'success' => true,
                 'message' => 'Profile updated successfully'
             ], 200),
             'https://account.nks.vn/api/nks/user' => Http::response([
                 'success' => true,
                 'data' => [
                     'cccd_front' => 'https://data.nks.vn/uploads/cccd/front.jpg',
                     'cccd_back' => 'https://data.nks.vn/uploads/cccd/back.jpg'
                 ]
             ], 200)
         ]);
 
         $payload = [
             'dob' => '1995-10-24',
             'pob' => 'Ba Đình, Hà Nội',
             'id_number' => '079195012345',
             'id_date' => '2022-10-20',
             'id_place' => 'Cục Cảnh sát QLHC về TTXH',
             'permanent_address' => '123 Phố Huế, Hai Bà Trưng, Hà Nội',
             'cccd_front' => 'data:image/jpeg;base64,' . base64_encode('fake front image'),
             'cccd_back' => 'data:image/jpeg;base64,' . base64_encode('fake back image'),
         ];
 
         $response = $this->actingAs($this->user)
             ->post(route('profile.cccd'), $payload);
 
         $response->assertRedirect(route('profile.index', ['tab' => 'profile', 'subtab' => 'cccd']));
         
         $this->user->refresh();
         $this->assertEquals('079195012345', $this->user->id_number);
         $this->assertEquals('https://data.nks.vn/uploads/cccd/front.jpg', $this->user->cccd_front);
         $this->assertEquals('https://data.nks.vn/uploads/cccd/back.jpg', $this->user->cccd_back);
     }
 }
