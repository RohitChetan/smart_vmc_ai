<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WardLocator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function detectWard(
        Request $request,
        WardLocator $wardLocator
    ): JsonResponse {
        $validated = $request->validate([
            'latitude' => [
                'required',
                'numeric',
                'between:-90,90',
            ],
            'longitude' => [
                'required',
                'numeric',
                'between:-180,180',
            ],
        ]);

        $ward = $wardLocator->findWard(
            (float) $validated['latitude'],
            (float) $validated['longitude']
        );

        if (!$ward) {
            return response()->json([
                'success' => false,
                'message' => 'Location is outside Vadodara Municipal Corporation wards.',
                'ward' => null,
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Ward detected successfully.',
            'ward' => [
                'id' => $ward->id,
                'ward_no' => $ward->ward_no,
                'name' => $ward->name,
                'address' => $ward->address,
            ],
            'location' => [
                'latitude' => (float) $validated['latitude'],
                'longitude' => (float) $validated['longitude'],
            ],
        ]);
    }
}