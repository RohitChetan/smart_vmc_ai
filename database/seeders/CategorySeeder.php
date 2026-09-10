<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Department;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Road & Pothole',
                'slug' => 'road-pothole',
                'department' => 'ROADS',
            ],
            [
                'name' => 'Road Damage',
                'slug' => 'road-damage',
                'department' => 'ROADS',
            ],
            [
                'name' => 'Footpath / Sidewalk',
                'slug' => 'footpath-sidewalk',
                'department' => 'ROADS',
            ],
            [
                'name' => 'Garbage / Waste',
                'slug' => 'garbage-waste',
                'department' => 'SOLID_WASTE',
            ],
            [
                'name' => 'Illegal Waste Dumping',
                'slug' => 'illegal-waste-dumping',
                'department' => 'SOLID_WASTE',
            ],
            [
                'name' => 'Drainage Blockage',
                'slug' => 'drainage-blockage',
                'department' => 'DRAINAGE',
            ],
            [
                'name' => 'Waterlogging',
                'slug' => 'waterlogging',
                'department' => 'DRAINAGE',
            ],
            [
                'name' => 'Sewerage Problem',
                'slug' => 'sewerage-problem',
                'department' => 'DRAINAGE',
            ],
            [
                'name' => 'Water Leakage',
                'slug' => 'water-leakage',
                'department' => 'WATER',
            ],
            [
                'name' => 'Water Supply Problem',
                'slug' => 'water-supply-problem',
                'department' => 'WATER',
            ],
            [
                'name' => 'Street Light',
                'slug' => 'street-light',
                'department' => 'ELECTRICAL',
            ],
            [
                'name' => 'Electrical Pole / Wire',
                'slug' => 'electrical-pole-wire',
                'department' => 'ELECTRICAL',
            ],
            [
                'name' => 'Tree / Fallen Tree',
                'slug' => 'tree-fallen-tree',
                'department' => 'PARKS',
            ],
            [
                'name' => 'Park / Garden',
                'slug' => 'park-garden',
                'department' => 'PARKS',
            ],
            [
                'name' => 'Building / Structure',
                'slug' => 'building-structure',
                'department' => 'BUILDING',
            ],
            [
                'name' => 'Other Civic Issue',
                'slug' => 'other-civic-issue',
                'department' => 'GENERAL',
            ],
        ];

        foreach ($categories as $item) {
            $department = Department::where(
                'code',
                $item['department']
            )->firstOrFail();

            Category::updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'name' => $item['name'],
                    'department_id' => $department->id,
                    'description' => null,
                    'is_active' => true,
                ]
            );
        }

        $this->command?->info('Categories seeded successfully.');
    }
}