<?php

namespace Database\Factories;

use App\Models\Warranty;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @extends Factory<Warranty>
 */
class WarrantyFactory extends Factory
{
    protected $model = Warranty::class;

    public function definition(): array
    {
        return [
            'public_token' => (string) Str::uuid(),
            'device_type' => null,
            'imei' => null,
            'warranty_expires_at' => null,
            'activated_at' => null,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'device_type' => null,
            'imei' => null,
            'warranty_expires_at' => null,
            'activated_at' => null,
        ]);
    }

    public function active(): static
    {
        $expiresAt = Carbon::now()->addDays(rand(30, 365));

        return $this->state(fn(array $attributes) => [
            'device_type' => fake()->randomElement(['iPhone 15 Pro', 'Samsung Galaxy S24', 'Google Pixel 8', 'OnePlus 12', 'Xiaomi Mi 14']),
            'imei' => $this->generateImei(),
            'warranty_expires_at' => $expiresAt,
            'activated_at' => Carbon::now()->subDays(rand(1, 30)),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn(array $attributes) => [
            'device_type' => fake()->randomElement(['iPhone 14', 'Samsung Galaxy S23', 'Google Pixel 7']),
            'imei' => $this->generateImei(),
            'warranty_expires_at' => Carbon::now()->subDays(rand(1, 90)),
            'activated_at' => Carbon::now()->subDays(rand(91, 180)),
        ]);
    }

    public function expiringSoon(int $days = 7): static
    {
        return $this->state(fn(array $attributes) => [
            'device_type' => fake()->randomElement(['iPhone 15', 'Samsung Galaxy S24']),
            'imei' => $this->generateImei(),
            'warranty_expires_at' => Carbon::now()->addDays($days),
            'activated_at' => Carbon::now()->subDays(rand(1, 30)),
        ]);
    }

    private function generateImei(): string
    {
        $digits = '';
        for ($i = 0; $i < 15; $i++) {
            $digits .= (string) random_int(0, 9);
        }
        return $digits;
    }
}
