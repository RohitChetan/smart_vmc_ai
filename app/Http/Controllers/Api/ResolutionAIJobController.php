<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ResolutionProof;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ResolutionAIJobController extends Controller
{
    /**
     * Get pending resolution-proof AI jobs.
     */
    public function pending(): JsonResponse
    {
        $proofs = ResolutionProof::with([
            'complaint.ward',
            'complaint.aiCategory',
            'complaint.department',
            'complaint.incident',
            'submittedBy',
        ])
            ->where('ai_status', 'pending')
            ->orderBy('id')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'count' => $proofs->count(),

            'jobs' => $proofs->map(function ($proof) {
                return [
                    'proof_id' => $proof->id,

                    'proof' => [
                        'id' => $proof->id,
                        'type' => $proof->type,
                        'file_path' => $proof->file_path,
                        'mime_type' => $proof->mime_type,
                        'latitude' => $proof->latitude,
                        'longitude' => $proof->longitude,
                        'location_accuracy' => $proof->location_accuracy,
                        'captured_at' => $proof->captured_at?->toISOString(),
                        'remarks' => $proof->remarks,
                    ],

                    'complaint' => [
                        'id' => $proof->complaint?->id,
                        'complaint_number' =>
                            $proof->complaint?->complaint_number,

                        'description' =>
                            $proof->complaint?->description,

                        'status' =>
                            $proof->complaint?->status,

                        'category' =>
                            $proof->complaint?->aiCategory?->name,

                        'department' =>
                            $proof->complaint?->department?->name,

                        'ward' => $proof->complaint?->ward ? [
                            'id' => $proof->complaint->ward->id,
                            'ward_no' => $proof->complaint->ward->ward_no,
                            'name' => $proof->complaint->ward->name,
                        ] : null,

                        'incident' => $proof->complaint?->incident ? [
                            'id' => $proof->complaint->incident->id,
                            'incident_number' =>
                                $proof->complaint->incident->incident_number,
                        ] : null,
                    ],

                    'submitted_by' => $proof->submittedBy ? [
                        'id' => $proof->submittedBy->id,
                        'name' => $proof->submittedBy->name,
                    ] : null,
                ];
            })->values(),
        ]);
    }

    /**
     * Get resolution-proof media for AI worker.
     */
    public function media(int $proofId): JsonResponse|\Symfony\Component\HttpFoundation\StreamedResponse
    {
        $proof = ResolutionProof::findOrFail($proofId);

        if (!Storage::disk('public')->exists($proof->file_path)) {
            return response()->json([
                'success' => false,
                'message' => 'Resolution proof media file not found.',
            ], 404);
        }

        return Storage::disk('public')->response(
            $proof->file_path,
            basename($proof->file_path),
            [
                'Content-Type' => $proof->mime_type,
            ]
        );
    }

    /**
     * Save AI verification result.
     */
    public function result(
        Request $request,
        int $proofId
    ): JsonResponse {
        $data = $request->validate([
            'status' => [
                'required',
                // 'in:verified,rejected',
                'in:verified,rejected,manual_review',
            ],

            'confidence' => [
                'required',
                'numeric',
                'between:0,1',
            ],

            'reason' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'detections' => [
                'nullable',
                'array',
            ],

            'raw_result' => [
                'nullable',
                'array',
            ],

            'model_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'model_version' => [
                'nullable',
                'string',
                'max:100',
            ],
        ]);

        $proof = ResolutionProof::with('complaint')
            ->findOrFail($proofId);

        $proof->update([
            'ai_status' => $data['status'],
            'ai_confidence' => $data['confidence'],
            'ai_result' => [
                'reason' => $data['reason'] ?? null,
                'detections' => $data['detections'] ?? [],
                'raw_result' => $data['raw_result'] ?? [],
                'model_name' => $data['model_name'] ?? 'local-resolution-ai',
                'model_version' => $data['model_version'] ?? '1.0',
            ],
            'verified_at' =>
                $data['status'] === 'verified'
                    ? now()
                    : null,
        ]);

        $complaint = $proof->complaint;

        /*
        |--------------------------------------------------------------------------
        | Verified Resolution
        |--------------------------------------------------------------------------
        */

        // if ($data['status'] === 'verified') {

        //     if ($complaint && $complaint->status === 'in_progress') {

        //         $complaint->update([
        //             'status' => 'verification_pending',
        //         ]);

        //         $complaint->statusHistory()->create([
        //             'status' => 'verification_pending',
        //             'changed_by' => null,
        //             'remarks' =>
        //                 $data['reason']
        //                 ?? 'Resolution proof verified by AI. Awaiting citizen verification.',
        //         ]);
        //     }
        // }

        if ($data['status'] === 'verified') {

            if ($complaint && $complaint->status === 'in_progress') {

                $complaint->update([
                    'status' => 'verification_pending',
                ]);

                $complaint->statusHistory()->create([
                    'status' => 'verification_pending',
                    'changed_by' => null,
                    'remarks' =>
                        $data['reason']
                        ?? 'Resolution proof verified by AI. Awaiting citizen verification.',
                ]);

                /*
                |--------------------------------------------------------------------------
                | Sync Incident Status
                |--------------------------------------------------------------------------
                | The incident must also wait for citizen verification.
                |--------------------------------------------------------------------------
                */

                if ($proof->incident_id) {
                    $incident = \App\Models\CivicIncident::find($proof->incident_id);

                    if ($incident && $incident->status === 'in_progress') {
                        $incident->update([
                            'status' => 'verification_pending',
                        ]);
                    }
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Rejected Resolution Proof
        |--------------------------------------------------------------------------
        */

        if ($data['status'] === 'rejected') {

            if ($complaint) {

                $complaint->statusHistory()->create([
                    'status' => $complaint->status,
                    'changed_by' => null,
                    'remarks' =>
                        $data['reason']
                        ?? 'Resolution proof rejected by AI verification.',
                ]);
            }
        }

        return response()->json([
            'success' => true,

            'message' =>
                $data['status'] === 'verified'
                    ? 'Resolution proof verified and complaint moved to citizen verification.'
                    : 'Resolution proof rejected by AI verification.',

            'proof' => [
                'id' => $proof->id,
                'complaint_id' => $proof->complaint_id,
                'incident_id' => $proof->incident_id,
                'ai_status' => $proof->ai_status,
                'ai_confidence' => $proof->ai_confidence,
                'verified_at' =>
                    $proof->verified_at?->toISOString(),
            ],

            'complaint' => $complaint ? [
                'id' => $complaint->id,
                'complaint_number' => $complaint->complaint_number,
                'status' => $complaint->status,
            ] : null,
        ]);
    }
}