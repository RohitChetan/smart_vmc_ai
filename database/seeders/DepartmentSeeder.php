<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Roads & Infrastructure',
                'code' => 'ROADS',
                'description' => 'Roads, potholes, footpaths, dividers and related infrastructure.',
            ],
            [
                'name' => 'Solid Waste Management',
                'code' => 'SOLID_WASTE',
                'description' => 'Garbage, waste dumping, cleanliness and waste collection.',
            ],
            [
                'name' => 'Drainage & Sewerage',
                'code' => 'DRAINAGE',
                'description' => 'Drainage, sewerage, blocked drains and waterlogging.',
            ],
            [
                'name' => 'Water Supply',
                'code' => 'WATER',
                'description' => 'Water leakage, pipeline and water supply related complaints.',
            ],
            [
                'name' => 'Electrical & Street Lighting',
                'code' => 'ELECTRICAL',
                'description' => 'Street lights, electrical poles and public lighting.',
            ],
            [
                'name' => 'Parks & Gardens',
                'code' => 'PARKS',
                'description' => 'Public parks, gardens, trees and green spaces.',
            ],
            [
                'name' => 'Building & Town Planning',
                'code' => 'BUILDING',
                'description' => 'Building, structure and town-planning related civic complaints.',
            ],
            [
                'name' => 'General Civic Services',
                'code' => 'GENERAL',
                'description' => 'Other civic complaints that do not fit another department.',
            ],
        ];

        foreach ($departments as $department) {
            Department::updateOrCreate(
                ['code' => $department['code']],
                $department
            );
        }

        $this->command?->info('Departments seeded successfully.');
    }
}