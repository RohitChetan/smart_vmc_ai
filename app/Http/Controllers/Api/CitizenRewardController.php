<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\CitizenReward;
use Illuminate\Http\Request;

class CitizenRewardController extends Controller
{
    public function rewards(Request $request)
    {
        $user = $request->user();

        $reward = CitizenReward::firstOrCreate(
            ['user_id' => $user->id],
            [
                'total_points' => 0,
                'level' => 'Citizen',
            ]
        );

        return response()->json([
            'success' => true,
            'reward' => [
                'total_points' => (int) $reward->total_points,
                'level' => $reward->level,
            ],
        ]);
    }

    public function certificates(Request $request)
    {
        $certificates = Certificate::query()
            ->where('user_id', $request->user()->id)
            ->with('complaint:id,complaint_number,ward_id')
            ->latest('issued_at')
            ->get()
            ->map(function ($certificate) {
                return [
                    'id' => $certificate->id,
                    'certificate_number' => $certificate->certificate_number,
                    'title' => $certificate->title,
                    'issued_at' => $certificate->issued_at?->toISOString(),
                    'complaint_number' =>
                        $certificate->complaint?->complaint_number,
                    'ward_id' =>
                        $certificate->complaint?->ward_id,
                ];
            });

        return response()->json([
            'success' => true,
            'certificates' => $certificates,
        ]);
    }
}