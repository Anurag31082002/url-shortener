<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $exists = DB::select(
            'SELECT id FROM users WHERE email = ?',
            ['superadmin@example.com']
        );

        if (count($exists) === 0) {
            DB::insert(
                'INSERT INTO users (name, email, password, role, company_id, email_verified_at, created_at, updated_at)
                 VALUES (?, ?, ?, ?, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)',
                [
                    'Super Admin',
                    'superadmin@example.com',
                    Hash::make('Admin@123'),
                    'SuperAdmin',
                ]
            );
        }
    }
}
