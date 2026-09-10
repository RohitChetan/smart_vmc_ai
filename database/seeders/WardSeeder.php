<?php

namespace Database\Seeders;

use App\Models\Ward;
use Illuminate\Database\Seeder;

class WardSeeder extends Seeder
{
    public function run(): void
    {
        $path = storage_path('app/gis/wards_vadodara.geojson');

        if (! file_exists($path)) {
            throw new \RuntimeException(
                "Ward GeoJSON file not found: {$path}"
            );
        }

        $data = json_decode(
            file_get_contents($path),
            true
        );

        if (
            ! is_array($data) ||
            ($data['type'] ?? null) !== 'FeatureCollection'
        ) {
            throw new \RuntimeException(
                'Invalid Ward GeoJSON FeatureCollection.'
            );
        }

        foreach ($data['features'] as $feature) {
            $properties = $feature['properties'] ?? [];
            $geometry = $feature['geometry'] ?? null;

            if (! isset($properties['ward_no'])) {
                continue;
            }

            Ward::updateOrCreate(
                [
                    'ward_no' => (int) $properties['ward_no'],
                ],
                [
                    'name' => $properties['ward_name'] ?? 'Ward ' . $properties['ward_no'],
                    'address' => $properties['ward_address'] ?? null,
                    'boundary_geojson' => $geometry,
                    'is_active' => true,
                ]
            );
        }

        $this->command?->info(
            'Vadodara ward boundaries imported successfully.'
        );
    }
}