<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = 'superadmin@sembark.test';

        $exists = DB::table('users')->where('email', $email)->exists();

        if ($exists) {
            return;
        }

        $now = now()->toDateTimeString();

        DB::insert(
            'INSERT INTO users (name, email, email_verified_at, password, role, company_id, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
            [
                'Super Admin',
                $email,
                $now,
                Hash::make('password'),
                UserRole::SuperAdmin->value,
                null,
                $now,
                $now,
            ]
        );
    }
}
