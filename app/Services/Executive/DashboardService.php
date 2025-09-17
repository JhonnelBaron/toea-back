<?php

namespace App\Services\Executive;

use App\Models\Nominee;

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
}