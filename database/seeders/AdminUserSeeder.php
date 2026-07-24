<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $name = config('admin.name');
        $email = config('admin.email');
        $password = config('admin.password');

        if (blank($email)) {
            throw new RuntimeException(
                'ADMIN_EMAIL is not configured.'
            );
        }

        if (blank($password)) {
            throw new RuntimeException(
                'ADMIN_PASSWORD is not configured.'
            );
        }

        $user = User::firstOrNew([
            'email' => $email,
        ]);

        $user->name = $name ?: 'Admin';
        $user->role = 'admin';

        /*
         * لا نغيّر كلمة مرور المدير عند كل Deploy.
         * يتم تعيينها فقط عند إنشاء المستخدم لأول مرة.
         */
        if (! $user->exists) {
            $user->password = Hash::make($password);
        }

        $user->save();

        $this->command?->info(
            "Admin user created or verified: {$email}"
        );
    }
}
