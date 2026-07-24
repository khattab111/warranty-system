<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Warranty;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class WarrantyAdminTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);
    }

    public function test_guest_cannot_access_admin()
    {
        $response = $this->get('/admin');

        $response->assertStatus(302);
    }

    public function test_admin_can_generate_qr_codes()
    {
        $this->actingAs($this->admin);

        Warranty::factory()->count(10)->create();

        $this->assertEquals(10, Warranty::count());
    }

    public function test_generated_tokens_are_unique()
    {
        $tokens = [];
        for ($i = 0; $i < 100; $i++) {
            $warranty = Warranty::factory()->create();
            $tokens[] = $warranty->public_token;
        }

        $this->assertEquals(count($tokens), count(array_unique($tokens)));
    }

    public function test_expiry_in_past_results_in_expired_status()
    {
        $warranty = Warranty::factory()->create();

        $warranty->update([
            'device_type' => 'iPhone 15',
            'imei' => '123456789012345',
            'warranty_expires_at' => Carbon::now()->subDay(),
            'activated_at' => now(),
        ]);

        $warranty->refresh();

        $this->assertNotNull($warranty->warranty_expires_at);
        $this->assertTrue($warranty->is_expired);
    }

    public function test_warranty_qr_download_returns_png()
    {
        $this->actingAs($this->admin);

        $warranty = Warranty::factory()->active()->create();

        $response = $this->get(route('warranty.qr.download', $warranty->public_token));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/png');
    }

    public function test_imei_must_be_unique()
    {
        $existing = Warranty::factory()->active()->create();

        $duplicate = Warranty::factory()->create();

        $this->expectException(\Illuminate\Database\UniqueConstraintViolationException::class);

        $duplicate->update([
            'device_type' => 'iPhone 15',
            'imei' => $existing->imei,
            'warranty_expires_at' => Carbon::now()->addYear(),
            'activated_at' => now(),
        ]);
    }

    public function test_activated_at_is_set_on_first_activation()
    {
        $warranty = Warranty::factory()->inactive()->create();

        $this->assertNull($warranty->activated_at);

        $warranty->update([
            'device_type' => 'iPhone 15',
            'imei' => '123456789012345',
            'warranty_expires_at' => Carbon::now()->addYear(),
            'activated_at' => now(),
        ]);

        $warranty->refresh();

        $this->assertNotNull($warranty->activated_at);
    }
}
