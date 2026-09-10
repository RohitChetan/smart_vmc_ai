<?php

namespace App\Services;

use App\Models\Ward;

class WardLocator
{
    /**
     * Find the ward containing the given GPS coordinate.
     */
    public function findWard(float $latitude, float $longitude): ?Ward
    {
        $wards = Ward::where('is_active', true)->get();

        foreach ($wards as $ward) {
            $geometry = $ward->boundary_geojson;

            if (!is_array($geometry)) {
                continue;
            }

            if ($this->pointInGeometry(
                $latitude,
                $longitude,
                $geometry
            )) {
                return $ward;
            }
        }

        return null;
    }

    private function pointInGeometry(
        float $latitude,
        float $longitude,
        array $geometry
    ): bool {
        $type = $geometry['type'] ?? null;
        $coordinates = $geometry['coordinates'] ?? [];

        if ($type === 'Polygon') {
            foreach ($coordinates as $ring) {
                if ($this->pointInPolygon(
                    $latitude,
                    $longitude,
                    $ring
                )) {
                    return true;
                }
            }

            return false;
        }

        if ($type === 'MultiPolygon') {
            foreach ($coordinates as $polygon) {
                foreach ($polygon as $ring) {
                    if ($this->pointInPolygon(
                        $latitude,
                        $longitude,
                        $ring
                    )) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Point-in-polygon using ray casting.
     *
     * IMPORTANT:
     * Our Vadodara GeoJSON stores coordinates as:
     * [latitude, longitude]
     */
    private function pointInPolygon(
        float $latitude,
        float $longitude,
        array $polygon
    ): bool {
        $inside = false;
        $count = count($polygon);

        if ($count < 3) {
            return false;
        }

        for ($i = 0, $j = $count - 1; $i < $count; $j = $i++) {

            // Our GeoJSON:
            // [latitude, longitude]

            $yi = (float) $polygon[$i][0]; // latitude
            $xi = (float) $polygon[$i][1]; // longitude

            $yj = (float) $polygon[$j][0]; // latitude
            $xj = (float) $polygon[$j][1]; // longitude

            $intersects = (
                (($yi > $latitude) !== ($yj > $latitude))
                &&
                (
                    $longitude <
                    ($xj - $xi)
                    * ($latitude - $yi)
                    / (($yj - $yi) ?: 0.0000000001)
                    + $xi
                )
            );

            if ($intersects) {
                $inside = !$inside;
            }
        }

        return $inside;
    }
}