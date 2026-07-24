<?php

namespace Tests\Feature;

use App\Models\Warranty;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class WarrantySecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_activation_data_rejects_duplicate_imei(): void
    {
        Warranty::factory()->active()->create([
            'imei' => '123456789012345',
        ]);

        $this->expectException(ValidationException::class);

        Warranty::validateActivationData([
            'device_type' => 'iPhone 15',
            'imei' => '123456789012345',
            'warranty_expires_at' => Carbon::now()->addYear()->toDateTimeString(),
        ]);
    }

    public function test_activation_data_sanitizes_and_normalizes_input(): void
    {
        $data = Warranty::validateActivationData([
            'device_type' => '  <b>iPhone 15</b>  ',
            'imei' => '123456789012345',
            'warranty_expires_at' => Carbon::now()->addYear()->toDateTimeString(),
        ]);

        $this->assertSame('iPhone 15', $data['device_type']);
        $this->assertSame('123456789012345', $data['imei']);
        $this->assertNotNull($data['warranty_expires_at']);
    }

    public function test_workflow_status_marks_printed_and_unlinked_warranties(): void
    {
        $printedPending = Warranty::factory()->create([
            'printed_at' => now(),
            'activated_at' => null,
        ]);

        $completed = Warranty::factory()->create([
            'device_type' => 'iPhone 15',
            'imei' => '123456789012345',
            'warranty_expires_at' => Carbon::now()->addYear(),
            'activated_at' => now(),
            'printed_at' => now(),
        ]);

        $this->assertSame('printed_pending', $printedPending->workflow_status);
        $this->assertSame('linked_completed', $completed->workflow_status);
    }
}
