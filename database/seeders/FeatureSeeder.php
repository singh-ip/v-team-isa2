<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class FeatureSeeder extends Seeder
{
    public function run(): void
    {
        $features = [
            'team',
            'stripe',
            'profileManagement',
            'cookiePolicy',
            'notifications'
        ];

        foreach ($features as $feature) {
            DB::statement(
                "INSERT INTO features (name, scope, value, created_at, updated_at)
                VALUES ('{$feature}Feature', '__global', 'true', NOW(), NOW())
                ON CONFLICT DO NOTHING"
            );
        }
    }
}
