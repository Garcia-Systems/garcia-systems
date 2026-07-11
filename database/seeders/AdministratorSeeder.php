<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdministratorSeeder extends Seeder
{
    public function run(): void
    {
        $adminEmail = config('services.admin.email') ?: env('ADMIN_EMAIL');
        $adminPassword = config('services.admin.password') ?: env('ADMIN_PASSWORD');
        $adminName = config('services.admin.name') ?: env('ADMIN_NAME', 'Garcia Systems Admin');

        if (blank($adminEmail)) {
            $this->command?->warn('Administrator bootstrap skipped: ADMIN_EMAIL is not configured.');
            return;
        }

        $existingAdmin = User::where('email', $adminEmail)->first();

        if ($existingAdmin) {
            $this->command?->info('Administrator bootstrap skipped: admin already exists and was left unchanged.');
            return;
        }

        if (blank($adminPassword)) {
            $this->command?->warn('Administrator bootstrap skipped: ADMIN_PASSWORD is required when creating the initial administrator.');
            return;
        }

        (new User())->forceFill([
            'name' => $adminName,
            'email' => $adminEmail,
            'password' => Hash::make($adminPassword),
            'email_verified_at' => now(),
        ])->save();

        $this->command?->info('Administrator account created for '.$adminEmail.'.');
    }
}
