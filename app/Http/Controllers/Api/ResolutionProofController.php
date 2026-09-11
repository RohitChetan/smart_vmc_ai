<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\ResolutionProof;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ResolutionProofController extends Controller
{
    /**
     * Upload field resolution proof.
     */
    public function store(
        Request $request,
        int $complaintId
    ): JsonResponse {
        $data = $request->validate([
            'media' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,webp,mp4,mov,avi',
                'max:51200',
            ],

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

            'location_accuracy' => [
                'nullable',
                'numeric',
                'min:0',
                'max:10000',
            ],

            'captured_at' => [
                'nullable',
                'date',
            ],

            'remarks' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $officer = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Officer must belong to a ward
        |--------------------------------------------------------------------------
        */

        if (!$officer->ward_id) {
            return response()->json([
                'success' => false,
                'message' => 'Field officer is not assigned to a ward.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Load complaint + incident assignment
        |--------------------------------------------------------------------------
        */

        $complaint = Complaint::with([
            'incident',
        ])
            ->where('id', $complaintId)
            ->where('ward_id', $officer->ward_id)
            ->whereHas('incident.assignments', function ($query) use ($officer) {
                $query
                    ->where('assigned_to', $officer->id)
                    ->whereNull('completed_at');
            })
            ->first();

        if (!$complaint) {
            return response()->json([
                'success' => false,
                'message' =>
                    'Complaint does not belong to your ward or its incident is not assigned to you.',
            ], 403);
        }

        /*
        |--------------------------------------------------------------------------
        | Resolution proof is allowed only while working
        |--------------------------------------------------------------------------
        */

        if ($complaint->status !== 'in_progress') {
            return response()->json([
                'success' => false,
                'message' =>
                    "Resolution proof can only be uploaded when complaint status is in_progress. Current status: {$complaint->status}.",
            ], 422);
        }

        if (!$complaint->incident) {
            return response()->json([
                'success' => false,
                'message' => 'Complaint is not linked to a civic incident.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Store file
        |--------------------------------------------------------------------------
        */

        $file = $request->file('media');

        $path = $file->store(
            'resolution-proofs/' . $complaint->id,
            'public'
        );

        $mimeType = $file->getMimeType();

        $type = Str::startsWith(
            $mimeType,
            'video/'
        )
            ? 'video'
            : 'image';

        /*
        |--------------------------------------------------------------------------
        | Create resolution proof
        |--------------------------------------------------------------------------
        */

        $proof = DB::transaction(function () use (
            $complaint,
            $officer,
            $data,
            $file,
            $path,
            $mimeType,
            $type
        ) {
            return ResolutionProof::create([
                'complaint_id' =>
                    $complaint->id,

                'incident_id' =>
                    $complaint->incident_id,

                'submitted_by' =>
                    $officer->id,

                'type' =>
                    $type,

                'file_path' =>
                    $path,

                'mime_type' =>
                    $mimeType,

                'file_size' =>
                    $file->getSize(),

                'latitude' =>
                    $data['latitude'],

                'longitude' =>
                    $data['longitude'],

                'location_accuracy' =>
                    $data['location_accuracy'] ?? null,

                'captured_at' =>
                    $data['captured_at'] ?? now(),

                'ai_status' =>
                    'pending',

                'remarks' =>
                    $data['remarks'] ?? null,
            ]);
        });

        return response()->json([
            'success' => true,

            'message' =>
                'Resolution proof uploaded successfully and queued for AI verification.',

            'proof' => [
                'id' =>
                    $proof->id,

                'complaint_id' =>
                    $proof->complaint_id,

                'incident_id' =>
                    $proof->incident_id,

                'type' =>
                    $proof->type,

                'ai_status' =>
                    $proof->ai_status,

                'location' => [
                    'latitude' =>
                        $proof->latitude,

                    'longitude' =>
                        $proof->longitude,

                    'accuracy' =>
                        $proof->location_accuracy,
                ],

                'captured_at' =>
                    $proof->captured_at?->toISOString(),
            ],
        ], 201);
    }
}