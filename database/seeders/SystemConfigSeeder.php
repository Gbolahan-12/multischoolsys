<?php

namespace Database\Seeders;

use App\Models\AssessmentConfig;
use App\Models\GradingScale;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SystemConfigSeeder extends Seeder
{
    public function run(): void
    {
        // Assessment Config
        Role::insert([
            [
                'name' => 'super-admin',
            ],
            [
                'name' => 'proprietor',
            ],
            [
                'name' => 'admin',
            ]
        ]);

    }
}
