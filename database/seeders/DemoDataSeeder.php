<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\ShortUrl;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::updateOrCreate(
            ['email' => 'demo@urlshortener.test'],
            ['name' => 'URL Shortener Demo']
        );

        $accounts = [
            [
                'name' => 'Demo Admin',
                'email' => 'admin@example.com',
                'role' => 'Admin',
                'password' => 'Admin@123',
            ],
            [
                'name' => 'Demo Member',
                'email' => 'member@example.com',
                'role' => 'Member',
                'password' => 'Member@123',
            ],
            [
                'name' => 'Demo Sales',
                'email' => 'sales@example.com',
                'role' => 'Sales',
                'password' => 'Sales@123',
            ],
            [
                'name' => 'Demo Manager',
                'email' => 'manager@example.com',
                'role' => 'Manager',
                'password' => 'Manager@123',
            ],
        ];

        foreach ($accounts as $account) {
            User::updateOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'role' => $account['role'],
                    'company_id' => $company->id,
                    'password' => Hash::make($account['password']),
                    'email_verified_at' => now(),
                ]
            );
        }

        $sampleUrls = [
            ['sales-demo', 'sales@example.com', 'https://example.com/sales-campaign', 24],
            ['manager-demo', 'manager@example.com', 'https://example.com/team-report', 13],
            ['member-demo', 'member@example.com', 'https://example.com/member-resource', 8],
        ];

        foreach ($sampleUrls as [$code, $email, $url, $hits]) {
            $user = User::where('email', $email)->firstOrFail();

            ShortUrl::updateOrCreate(
                ['code' => $code],
                [
                    'user_id' => $user->id,
                    'company_id' => $company->id,
                    'long_url' => $url,
                    'hits' => $hits,
                ]
            );
        }
    }
}
