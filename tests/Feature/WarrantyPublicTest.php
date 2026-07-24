<?php

namespace Tests\Feature;

use App\Models\Warranty;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class WarrantyPublicTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_warranty_page_loads()
    {
        $warranty = Warranty::factory()->active()->create();

        $response = $this->get(route('warranty.show', $warranty->public_token));

        $response->assertStatus(200);
        $response->assertSee($warranty->device_type);
    }

    public function test_invalid_token_returns_404()
    {
        $response = $this->get(route('warranty.show', Str::uuid()));

        $response->assertStatus(404);
    }

    public function test_unactivated_warranty_shows_appropriate_message()
    {
        $warranty = Warranty::factory()->inactive()->create();

        $response = $this->get(route('warranty.show', $warranty->public_token));

        $response->assertStatus(200);
        $response->assertSee('لم يتم ربطه بجهاز');
    }

    public function test_active_warranty_shows_correct_status()
    {
        $warranty = Warranty::factory()->active()->create();

        $response = $this->get(route('warranty.show', $warranty->public_token));

        $response->assertStatus(200);
        $response->assertSee('ساري');
    }

    public function test_expired_warranty_shows_correct_status()
    {
        $warranty = Warranty::factory()->expired()->create();

        $response = $this->get(route('warranty.show', $warranty->public_token));

        $response->assertStatus(200);
        $response->assertSee('انتهت');
    }

    public function test_imei_is_masked()
    {
        $warranty = Warranty::factory()->active()->create();

        $response = $this->get(route('warranty.show', $warranty->public_token));

        $response->assertDontSee($warranty->imei);
        $response->assertSee(substr($warranty->imei, -4));
    }
}
