<?php

namespace App\Services\Executive;

use App\Models\ACriteria;
use App\Models\BCriteria;
use App\Models\CCriteria;
use App\Models\DCriteria;
use App\Models\ECriteria;
use App\Models\Evaluation\BroScore;
use App\Models\Evaluation\BroSummary;
use App\Models\Evaluation\UserNomineeStatus;
use App\Models\Nominee;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Contracts\Providers\JWT;
use Tymon\JWTAuth\Facades\JWTAuth;

class EvaluationService
{
 
 protected $officeMap = [
        'Administrative Service' => 'as',
        'Legal Division' => 'legal',
        'Certification Office' => 'co',
        'Financial and Management Service' => 'fms',
        'National Institute for Technical Education and Skills Development' => 'nitesd',
        'Public Information and Assistance Division' => 'piad',
        'Planning Office' => 'planning',
        'Partnership and Linkages Office' => 'plo',
        'Regional Operations Management Office' => 'romo',
        'Information and Communication Technology Office' => 'icto',
        'World Skills' => 'ws',
        'Gender and Development TESDA Women Center' => 'gadtwc',
        'Community-Based Technical Vocational Education and Training Office' => 'cbtveto'
    ];

    protected $criteriaModels = [
        'a' => ACriteria::class,
        'b' => BCriteria::class,
        'c' => CCriteria::class,
        'd' => DCriteria::class,
        'e' => ECriteria::class,
    ];

    /**
     * Get criteria + requirements for a user's office
     */
    public function getCriteriaForOffice(string $office)
    {
        $officeKey = $this->officeMap[$office] ?? null;

        if (!$officeKey) return [];

        $result = [];

        $relationshipMap = [
            'a' => 'aRequirements',
            'b' => 'bRequirements',
            'c' => 'cRequirements',
            'd' => 'dRequirements',
            'e' => 'eRequirements',
        ];

        foreach ($this->criteriaModels as $key => $modelClass) {
            $relationship = $relationshipMap[$key]; // get the correct relationship string

            $criterias = $modelClass::with($relationship)
                ->where($officeKey, true)
                ->get();

            $result[$key] = $criterias;
        }

        return $result;
    }


      private function fetchCriteria($modelClass, $relationship, string $office)
    {
        $officeKey = $this->officeMap[$office] ?? null;
        if (!$officeKey) {
            return [
                'data' => [],
                'message' => 'Invalid office',
            ];
        }

        $criterias = $modelClass::with($relationship)
            ->where($officeKey, true)
            ->get();

        if ($criterias->isEmpty()) {
            return [
                'data' => [],
                'message' => 'No criteria found for this office',
            ];
        }

        return [
            'data' => $criterias,
            'message' => 'Criteria retrieved successfully',
        ];
    }

    // ------------------- Public functions -------------------

    public function getACriteriaForOffice(string $office)
    {
        return $this->fetchCriteria(ACriteria::class, 'aRequirements', $office);
    }

    public function getBCriteriaForOffice(string $office)
    {
        return $this->fetchCriteria(BCriteria::class, 'bRequirements', $office);
    }

    public function getCCriteriaForOffice(string $office)
    {
        return $this->fetchCriteria(CCriteria::class, 'cRequirements', $office);
    }

    public function getDCriteriaForOffice(string $office)
    {
        return $this->fetchCriteria(DCriteria::class, 'dRequirements', $office);
    }

    public function getECriteriaForOffice(string $office)
    {
        return $this->fetchCriteria(ECriteria::class, 'eRequirements', $office);
    }
    
    public function get($id)
    {
        $nominee = Nominee::find($id);
        if (!$nominee) {
            return [
                'status' => 404,
                'message' => 'Nominee not found',
                'data' => null
            ];
        }

        return [
            'status' => 200,
            'message' => 'Nominee retrieved successfully',
            'data' => $nominee
        ];
    }

   //scoring evaluation
    public function addScore(array $data)
    {
        return DB::transaction(function () use ($data) {
            // 1. Ensure nominee has a BroSummary
            $summary = BroSummary::firstOrCreate(
                ['nominee_id' => $data['nominee_id']],
                [
                    'final_score' => 0,
                    'bro_total'   => 0,
                    'bro_a'       => 0,
                    'bro_b'       => 0,
                    'bro_c'       => 0,
                    'bro_d'       => 0,
                    'bro_e'       => 0
                ]
            );

            // 2. Handle attachment if present
            $attachmentPath = null;
            $attachmentName = null;
            $attachmentType = null;

            if (isset($data['attachment']) && $data['attachment'] instanceof \Illuminate\Http\UploadedFile) {
                $file = $data['attachment'];
                $attachmentPath = $file->store('attachments', 'public');
                $attachmentName = $file->getClientOriginalName();
                $attachmentType = $file->getClientMimeType();
            }

            // 3. Update or create BroScore entry
            $broScore = BroScore::updateOrCreate(
                [
                    'user_id'        => JWTAuth::user()->id,
                    'nominee_id'     => $data['nominee_id'],
                    'criteria_table' => $data['criteria_table'],
                    'criteria_id'    => $data['criteria_id'],
                ],
                [
                    'bro_summary_id'  => $summary->id,
                    'score'           => $data['score'],
                    'remarks'         => $data['remarks'] ?? null,
                    'attachment_path' => $attachmentPath,
                    'attachment_name' => $attachmentName,
                    'attachment_type' => $attachmentType,
                ]
            );

            // 4. Update summary based on criteria_table
            $column = match ($data['criteria_table']) {
                'a_criterias' => 'bro_a',
                'b_criterias' => 'bro_b',
                'c_criterias' => 'bro_c',
                'd_criterias' => 'bro_d',
                'e_criterias' => 'bro_e',
                default       => null,
            };

            if ($column) {
                // Recalculate from all scores for this nominee (avoids double-counting on update)
                $summary->{$column} = BroScore::where('nominee_id', $data['nominee_id'])
                    ->where('criteria_table', $data['criteria_table'])
                    ->sum('score');

                $summary->bro_total = 
                    $summary->bro_a + $summary->bro_b + $summary->bro_c + $summary->bro_d + $summary->bro_e;
                $summary->save();
            }

            return $broScore;
        });
    }

    public function getScore($id)
    {
        $data = BroScore::find($id);
        if (!$data) {
            return [
                'status' => 404,
                'message' => 'Score not found',
                'data' => null
            ];
        }

        return [
            'status' => 200,
            'message' => 'Score retrieved successfully',
            'data' => $data
        ];
    }

    public function getScoresForNominee($nomineeId)
    {
        // $scores = BroScore::where('nominee_id', $nomineeId)
        //     ->get();

        // return [
        //     'status' => 200,
        //     'message' => 'Scores retrieved successfully',
        //     'data' => $scores
        // ];
            $userId = JWTAuth::user()->id;

            $scores = BroScore::where('nominee_id', $nomineeId)
                ->where('user_id', $userId)
                ->get();

            return [
                'status'  => 200,
                'message' => 'Scores retrieved successfully',
                'data'    => $scores
            ];
    }

    public function markAsDone($nomineeId)
    {
        $userId = JWTAuth::user()->id;

        $status = UserNomineeStatus::updateOrCreate(
            [
                'user_id'    => $userId,
                'nominee_id' => $nomineeId,
            ],
            [
                'status' => 'done',
            ]
        );
        return [
            'status'  => 200,
            'message' => 'Scores marked as done',
        ];
    }

    // In EvaluationService.php
    public function getStatus($nomineeId)
    {
        $userId = JWTAuth::user()->id;

        $record = UserNomineeStatus::where('user_id', $userId)
            ->where('nominee_id', $nomineeId)
            ->first();

        return [
            'status' => 200,
            'data' => [
                'nominee_id' => $nomineeId,
                'user_id'    => $userId,
                'status'      => $record ? $record->status : 'pending',
            ]
        ];
    }




}