<?php

namespace App\Services\Executive;

use App\Models\Evaluation\ExecutiveScore;
use App\Models\Nominee;
use Tymon\JWTAuth\Facades\JWTAuth;

class DashboardService
{
    public function getBroNominees()
    {
        $nominees = Nominee::with('user')
            ->where('nominee_type', 'BRO')
            ->get();

        return [
            'status' => 200,
            'message' => 'BRO Nominees retrieved successfully.',
            'data' => $nominees
        ];
    }
    // Evaluation related methods would go here

    public function getScoreRating()
    {
        $user = JWTAuth::user();

        // Get all ExecutiveScores for the user
        $progress = ExecutiveScore::where('user_id', $user->id)
            ->get(['nominee_id','total_score','overall_score','completion_rate']);

        return [
            'status' => 200,
            'message' => 'Score Rating retrieved successfully.',
            'user_id' => $user->id,
            'overall_completion_rate' => $user->overall_completion_rate, // only once
            'data' => $progress
        ];
    }

}