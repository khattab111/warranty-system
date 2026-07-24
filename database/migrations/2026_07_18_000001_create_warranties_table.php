<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warranties', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_token')->unique();
            $table->string('device_type', 150)->nullable();
            $table->string('imei', 15)->nullable()->unique();
            $table->timestamp('warranty_expires_at')->nullable()->index();
            $table->timestamp('activated_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warranties');
    }
};
