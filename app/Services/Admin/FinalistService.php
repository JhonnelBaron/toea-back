<?php

namespace App\Services\Admin;

use App\Models\ACriteria;
use App\Models\BCriteria;
use App\Models\CCriteria;
use App\Models\DCriteria;
use App\Models\ECriteria;
use App\Models\Evaluation\BroScore;
use App\Models\Evaluation\BroSummary;
use App\Models\Nominee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinalistService
{
    public function getFinalists(Request $request)
    {
        // Require nominee_type if any filter is applied
        if ($request->filled('nominee_category') && !$request->filled('nominee_type')) {
            return [
                'status' => 400,
                'message' => 'Filtering by category requires a nominee_type parameter.',
                'data' => []
            ];
        }

        // Run based on type (BRO, GP, or TTI)
        $broFinalists = $this->get($request);
        $gpFinalists = $this->getGpFinalist($request);
        $ttiFinalists = $this->getTtiFinalist($request);

        // Combine results by type
        $finalists = [
            'BRO' => $broFinalists,
            'GP' => $gpFinalists,
            'TTI' => $ttiFinalists,
        ];

        return [
            'status' => 200,
            'message' => 'Finalists retrieved successfully.',
            'data' => $finalists
        ];
    }


    public function totalFinalists(Request $request)
    {
        // Get the count of all finalists
        $broFinalists = $this->getBroFinalist();
        $gpFinalists = $this->getGpFinalist($request);
        $ttiFinalists = $this->getTtiFinalist($request);

        // Calculate total counts
        $broFinalistsCount = $broFinalists->count();
        $gpFinalistsCount = $gpFinalists->count();
        $ttiFinalistsCount = $ttiFinalists->count();

        // Calculate counts by category for each group
        $broCategoryCount = $broFinalists->groupBy('nominee_category')->map->count();
        $gpCategoryCount = $gpFinalists->groupBy('nominee_category')->map->count();
        $ttiCategoryCount = $ttiFinalists->groupBy('nominee_category')->map->count();

        // Calculate total finalists
        $totalCount = $broFinalistsCount + $gpFinalistsCount + $ttiFinalistsCount;

        return [
            'status' => 200,
            'message' => 'Total finalists count retrieved successfully.',
            'data' => $totalCount,
            'BRO' => [
                'count' => $broFinalistsCount,
                'category_count' => $broCategoryCount
            ],
            'GP' => [
                'count' => $gpFinalistsCount,
                'category_count' => $gpCategoryCount
            ],
            'TTI' => [
                'count' => $ttiFinalistsCount,
                'category_count' => $ttiCategoryCount
            ],
        ];
    }


    private function getBroFinalist()
    {
        $broFinalists = BroSummary::with('nominee')  // Eager load the 'nominee' relation
            ->where('endorse_externals', true)
            ->get()
            ->map(function ($broFinalist) {
                // Return an array of all BroSummary data, along with related nominee data
                return [
                    'id' => $broFinalist->id,  // Example: returning all fields from BroSummary
                    'nominee_id' => $broFinalist->nominee_id,  // Return other columns you need from BroSummary
                    'endorse_externals' => $broFinalist->endorse_externals,  // etc.
                    'nominee_name' => $broFinalist->nominee->nominee_name,  // Related nominee data
                    'nominee_category' => $broFinalist->nominee->nominee_category,
                    'region' => $broFinalist->nominee->region,
                    // You can add more columns from both BroSummary and Nominee as needed
                ];
            });

        return $broFinalists;
    }

    private function getGpFinalist(Request $request)
    {
        $gpFinalists = DB::connection('mysql_jd')
            ->table('combined_evaluations as ce')
            ->join('nominees as n', 'ce.user_id', '=', 'n.user_id')
            ->where('ce.status', 'endorsed_to_external_validator')
            ->where('n.nominee_type', 'GP');
        // 🟦 Add filters if provided
            if ($request->filled('nominee_category')) {
                $gpFinalists->where('n.nominee_category', $request->nominee_category);
            }

            if ($request->filled('nominee_name')) {
                $gpFinalists->where('n.nominee_name', 'like', '%' . $request->nominee_name . '%');
            }

            $gpFinalists = $gpFinalists
                ->select(
                    'ce.id as combined_evaluation_id',
                    'ce.user_id',
                    'n.nominee_name',
                    'n.nominee_category',
                    'ce.se_total_score',
                    'ce.se_category_a_score',
                    'ce.se_category_b_score',
                    'ce.se_category_c_score',
                    'ce.se_category_d_score',
                    'ce.se_category_e_score',
                    'ce.ev1_total_score',
                    'ce.ev1_category_a_score',
                    'ce.ev1_category_b_score',
                    'ce.ev1_category_c_score',
                    'ce.ev1_category_d_score',
                    'ce.ev1_category_e_score',
                    'ce.ev2_total_score',
                    'ce.ev2_category_a_score',
                    'ce.ev2_category_b_score',
                    'ce.ev2_category_c_score',
                    'ce.ev2_category_d_score',
                    'ce.ev2_category_e_score',
                    'ce.ev3_total_score',
                    'ce.ev3_category_a_score',
                    'ce.ev3_category_b_score',
                    'ce.ev3_category_c_score',
                    'ce.ev3_category_d_score',
                    'ce.ev3_category_e_score',
                )
                ->distinct()
                ->get();


    // Step 2: Iterate over each finalist and compute percentages
    foreach ($gpFinalists as $item) {

            $scores = [
        $item->se_total_score,
        $item->ev1_total_score,
        $item->ev2_total_score,
        $item->ev3_total_score
    ];

    // Filter out null or zero values if needed
    $validScores = array_filter($scores, fn($s) => $s !== null && $s > 0);

    // Compute average
    $item->average_score = count($validScores) > 0 
        ? round(array_sum($validScores) / count($validScores), 2) 
        : 0;

        // Get percentages for categories A–E (including the count)
        $item->A_percentage = $this->countApercentageGP($item->combined_evaluation_id);
        $item->B_percentage = $this->countBpercentageGP($item->combined_evaluation_id);
        $item->C_percentage = $this->countCpercentageGP($item->combined_evaluation_id);
        $item->D_percentage = $this->countDpercentageGP($item->combined_evaluation_id);
        $item->E_percentage = $this->countEpercentageGP($item->combined_evaluation_id);

        // Step 4: Compute total percentage based on A–E
        $totalPercentage = ($item->A_percentage + $item->B_percentage + $item->C_percentage +
                            $item->D_percentage + $item->E_percentage) / 5;

        $item->total_percentage = round($totalPercentage, 2);

        // EV1 progress
        $item->ev1_progress = [
            'A' => $this->countExternalCategoryPercentage($item->combined_evaluation_id, 'A', 272),
            'B' => $this->countExternalCategoryPercentage($item->combined_evaluation_id, 'B', 272),
            'C' => $this->countExternalCategoryPercentage($item->combined_evaluation_id, 'C', 272),
            'D' => $this->countExternalCategoryPercentage($item->combined_evaluation_id, 'D', 272),
            'E' => $this->countExternalCategoryPercentage($item->combined_evaluation_id, 'E', 272),
        ];
        // Compute total percentage as average
        $item->ev1_total_percentage = round(array_sum($item->ev1_progress) / count($item->ev1_progress), 2);

        // EV2 progress
        $item->ev2_progress = [
            'A' => $this->countExternalCategoryPercentage($item->combined_evaluation_id, 'A', 273),
            'B' => $this->countExternalCategoryPercentage($item->combined_evaluation_id, 'B', 273),
            'C' => $this->countExternalCategoryPercentage($item->combined_evaluation_id, 'C', 273),
            'D' => $this->countExternalCategoryPercentage($item->combined_evaluation_id, 'D', 273),
            'E' => $this->countExternalCategoryPercentage($item->combined_evaluation_id, 'E', 273),
        ];
        $item->ev2_total_percentage = round(array_sum($item->ev2_progress) / count($item->ev2_progress), 2);

        // EV3 progress
        $item->ev3_progress = [
            'A' => $this->countExternalCategoryPercentage($item->combined_evaluation_id, 'A', 274),
            'B' => $this->countExternalCategoryPercentage($item->combined_evaluation_id, 'B', 274),
            'C' => $this->countExternalCategoryPercentage($item->combined_evaluation_id, 'C', 274),
            'D' => $this->countExternalCategoryPercentage($item->combined_evaluation_id, 'D', 274),
            'E' => $this->countExternalCategoryPercentage($item->combined_evaluation_id, 'E', 274),
        ];
        $item->ev3_total_percentage = round(array_sum($item->ev3_progress) / count($item->ev3_progress), 2);

        // Finally, compute overall_percentage (each is 25%)
        $item->overall_percentage = round((
            ($item->total_percentage ?? 0) +
            ($item->ev1_total_percentage ?? 0) +
            ($item->ev2_total_percentage ?? 0) +
            ($item->ev3_total_percentage ?? 0)
        ) / 4, 2);
    }
    

        return $gpFinalists;
    }

    private function getTtiFinalist(Request $request)
    {
        $ttiFinalists = DB::connection('mysql_jd')
            ->table('combined_evaluations as ce')
            ->join('nominees as n', 'ce.user_id', '=', 'n.user_id')
            ->where('ce.status', 'endorsed_to_external_validator')
            ->where('n.nominee_type', 'BTI');
              // 🟦 Add filters if provided
    if ($request->filled('nominee_category')) {
        $ttiFinalists->where('n.nominee_category', $request->nominee_category);
    }

    if ($request->filled('nominee_name')) {
        $ttiFinalists->where('n.nominee_name', 'like', '%' . $request->nominee_name . '%');
    }

    $ttiFinalists = $ttiFinalists
        ->select(
            'ce.id as combined_evaluation_id',
            'ce.user_id',
            'n.nominee_name',
            'n.nominee_category',
            'ce.se_total_score',
            'ce.se_category_a_score',
            'ce.se_category_b_score',
            'ce.se_category_c_score',
            'ce.se_category_d_score',
            'ce.se_category_e_score',
            'ce.ev1_total_score',
            'ce.ev1_category_a_score',
            'ce.ev1_category_b_score',
            'ce.ev1_category_c_score',
            'ce.ev1_category_d_score',
            'ce.ev1_category_e_score',
            'ce.ev2_total_score',
            'ce.ev2_category_a_score',
            'ce.ev2_category_b_score',
            'ce.ev2_category_c_score',
            'ce.ev2_category_d_score',
            'ce.ev2_category_e_score',
            'ce.ev3_total_score',
            'ce.ev3_category_a_score',
            'ce.ev3_category_b_score',
            'ce.ev3_category_c_score',
            'ce.ev3_category_d_score',
            'ce.ev3_category_e_score',
        )
        ->distinct()
        ->get();


            // Step 2: Iterate over each finalist and compute percentages
    foreach ($ttiFinalists as $item) {
            $scores = [
        $item->se_total_score,
        $item->ev1_total_score,
        $item->ev2_total_score,
        $item->ev3_total_score
    ];

    // Filter out null or zero values if needed
    $validScores = array_filter($scores, fn($s) => $s !== null && $s > 0);

    // Compute average
    $item->average_score = count($validScores) > 0 
        ? round(array_sum($validScores) / count($validScores), 2) 
        : 0;
        // Get percentages for categories A–E (including the count)
        $item->A_percentage = $this->countApercentageTTI($item->combined_evaluation_id);
        $item->B_percentage = $this->countBpercentageTTI($item->combined_evaluation_id);
        $item->C_percentage = $this->countCpercentageTTI($item->combined_evaluation_id);
        $item->D_percentage = $this->countDpercentageTTI($item->combined_evaluation_id);
        $item->E_percentage = $this->countEpercentageTTI($item->combined_evaluation_id);

        // Step 4: Compute total percentage based on A–E
        $totalPercentage = ($item->A_percentage + $item->B_percentage + $item->C_percentage +
                            $item->D_percentage + $item->E_percentage) / 5;

        $item->total_percentage = round($totalPercentage, 2);

        // EV1 progress
        $item->ev1_progress = [
            'A' => $this->countExternalCategoryPercentage($item->combined_evaluation_id, 'A', 272),
            'B' => $this->countExternalCategoryPercentage($item->combined_evaluation_id, 'B', 272),
            'C' => $this->countExternalCategoryPercentage($item->combined_evaluation_id, 'C', 272),
            'D' => $this->countExternalCategoryPercentage($item->combined_evaluation_id, 'D', 272),
            'E' => $this->countExternalCategoryPercentage($item->combined_evaluation_id, 'E', 272),
        ];
        // Compute total percentage as average
        $item->ev1_total_percentage = round(array_sum($item->ev1_progress) / count($item->ev1_progress), 2);

        // EV2 progress
        $item->ev2_progress = [
            'A' => $this->countExternalCategoryPercentage($item->combined_evaluation_id, 'A', 273),
            'B' => $this->countExternalCategoryPercentage($item->combined_evaluation_id, 'B', 273),
            'C' => $this->countExternalCategoryPercentage($item->combined_evaluation_id, 'C', 273),
            'D' => $this->countExternalCategoryPercentage($item->combined_evaluation_id, 'D', 273),
            'E' => $this->countExternalCategoryPercentage($item->combined_evaluation_id, 'E', 273),
        ];
        $item->ev2_total_percentage = round(array_sum($item->ev2_progress) / count($item->ev2_progress), 2);

        // EV3 progress
        $item->ev3_progress = [
            'A' => $this->countExternalCategoryPercentage($item->combined_evaluation_id, 'A', 274),
            'B' => $this->countExternalCategoryPercentage($item->combined_evaluation_id, 'B', 274),
            'C' => $this->countExternalCategoryPercentage($item->combined_evaluation_id, 'C', 274),
            'D' => $this->countExternalCategoryPercentage($item->combined_evaluation_id, 'D', 274),
            'E' => $this->countExternalCategoryPercentage($item->combined_evaluation_id, 'E', 274),
        ];
        $item->ev3_total_percentage = round(array_sum($item->ev3_progress) / count($item->ev3_progress), 2);

        // Finally, compute overall_percentage (each is 25%)
        $item->overall_percentage = round((
            ($item->total_percentage ?? 0) +
            ($item->ev1_total_percentage ?? 0) +
            ($item->ev2_total_percentage ?? 0) +
            ($item->ev3_total_percentage ?? 0)
        ) / 4, 2);
        
    }

        return $ttiFinalists;
    }


     public function get(Request $request)
    {
        $query = BroSummary::with('nominee')
        ->where('endorse_externals', true);
        
        // Filter by nominee_type
        if ($request->filled('nominee_type')) {
            $query->whereHas('nominee', function ($q) use ($request) {
                $q->where('nominee_type', $request->nominee_type);
            });
        }

        // Filter by nominee_category
        if ($request->filled('nominee_category')) {
            $query->whereHas('nominee', function ($q) use ($request) {
                $q->where('nominee_category', $request->nominee_category);
            });
        }

        // Search by nominee_name (partial match)
        if ($request->filled('nominee_name')) {
            $query->whereHas('nominee', function ($q) use ($request) {
                $q->where('nominee_name', 'like', '%' . $request->nominee_name . '%');
            });
        }

        $query->orderByDesc('bro_total');
        $data = $query->get();

        foreach ($data as $item) {
            $nomineeId = $item->nominee_id;

            $item->A_percentage = $this->countApercentage($nomineeId);
            $item->B_percentage = $this->countBpercentage($nomineeId);
            $item->C_percentage = $this->countCpercentage($nomineeId);
            $item->D_percentage = $this->countDpercentage($nomineeId);
            $item->E_percentage = $this->countEpercentage($nomineeId);
            $item->total_percentage = $this->totalpercentage($nomineeId);

            $item->external_scores = $this->BroExternals($nomineeId);

            $item->overall_percentage = round((
                ($item->total_percentage ?? 0) +
                ($item->external_scores['ev1_total_percentage'] ?? 0) +
                ($item->external_scores['ev2_total_percentage'] ?? 0) +
                ($item->external_scores['ev3_total_percentage'] ?? 0)
            ) / 4, 2);
        }

        return [
            'status' => 200,
            'message' => 'BRO summaries retrieved successfully.',
            'data' => $data,
        ];
    }

private function BroExternals($nomineeId)
{
    // Fetch evaluator totals
    $evaluation = DB::connection('mysql_jd')
        ->table('combined_evaluations as ce')
        ->where('ce.laravel_nominee_id', $nomineeId)
        ->where('ce.status', 'endorsed_to_external_validator')
        ->select(
            'ce.id',
            'ce.ev1_total_score',
            'ce.ev1_category_a_score',
            'ce.ev1_category_b_score',
            'ce.ev1_category_c_score',
            'ce.ev1_category_d_score',
            'ce.ev1_category_e_score',
            'ce.ev2_total_score',
            'ce.ev2_category_a_score',
            'ce.ev2_category_b_score',
            'ce.ev2_category_c_score',
            'ce.ev2_category_d_score',
            'ce.ev2_category_e_score',
            'ce.ev3_total_score',
            'ce.ev3_category_a_score',
            'ce.ev3_category_b_score',
            'ce.ev3_category_c_score',
            'ce.ev3_category_d_score',
            'ce.ev3_category_e_score'
        )
        ->first();

    if (!$evaluation) {
        return null;
    }

    // Get bro_total from BroSummary
    $broSummary = BroSummary::where('nominee_id', $nomineeId)->first();
    if (!$broSummary) return null;

    $bro_total = $broSummary->bro_total ?? 0;

    // Compute average score including bro_total + evaluators
    $scores = [
        $bro_total,
        $evaluation->ev1_total_score ?? 0,
        $evaluation->ev2_total_score ?? 0,
        $evaluation->ev3_total_score ?? 0
    ];

    $validScores = array_filter($scores, fn($v) => $v > 0);
    $average_score = count($validScores) > 0
        ? round(array_sum($validScores) / count($validScores), 2)
        : 0;

    // Optional: compute evaluator category percentages
    $validators = [
        'ev1' => 272,
        'ev2' => 273,
        'ev3' => 274
    ];

    $criteriaModels = [
        'A' => ACriteria::class,
        'B' => BCriteria::class,
        'C' => CCriteria::class,
        'D' => DCriteria::class,
        'E' => ECriteria::class
    ];

    $nomineeCategory = strtolower($broSummary->nominee->nominee_category ?? '');
    $results = [];

    foreach ($validators as $label => $validatorId) {
        $evaluatorResult = [];
        foreach ($criteriaModels as $criteriaType => $model) {
            $givenScores = DB::connection('mysql_jd')
                ->table('external_validator_scores')
                ->where('combined_evaluation_id', $evaluation->id)
                ->where('evaluator_id', $validatorId)
                ->where('criteria_type', $criteriaType)
                ->count();

            $criteriaQuery = $model::query();
            switch ($nomineeCategory) {
                case 'small':
                    $criteriaQuery->where('bro_small', true);
                    break;
                case 'medium':
                    $criteriaQuery->where('bro_medium', true);
                    break;
                case 'large':
                    $criteriaQuery->where('bro_large', true);
                    break;
            }

            $criteriaCount = $criteriaQuery->count();
            $percentage = $criteriaCount > 0 ? round(($givenScores / $criteriaCount) * 100, 2) : 0;
            $evaluatorResult["{$label}_category_" . strtolower($criteriaType) . "_percentage"] = $percentage;
        }

        $categoryPercentages = array_values($evaluatorResult);
        $evaluatorResult["{$label}_total_percentage"] = count($categoryPercentages)
            ? round(array_sum($categoryPercentages) / count($categoryPercentages), 2)
            : 0;

        $results = array_merge($results, $evaluatorResult);
    }

    // Return evaluator totals + computed average
    return array_merge(collect($evaluation)->toArray(), $results, [
        'bro_total' => $bro_total,
        'average_score' => $average_score
    ]);
}



    private function countApercentage($nomineeId)
    {
        $data = BroScore::where('criteria_table', 'a_criterias')
            ->where('nominee_id', $nomineeId)
            ->count();

        $criteria = ACriteria::where('bro_small', true)
        ->where('bro_medium', true)
        ->where('bro_large', true)
        ->count();

        $percentage = ($data / $criteria) * 100;
        return $percentage;
    }

    private function countBpercentage($nomineeId)
{
    // Get how many scores exist for this nominee under b_criterias
    $data = BroScore::where('criteria_table', 'b_criterias')
        ->where('nominee_id', $nomineeId)
        ->count();

    // Find the nominee to check their category
    $nominee = Nominee::find($nomineeId);

    // Default to 0 to avoid division by zero
    $criteria = 0;

    if ($nominee) {
        switch (strtolower($nominee->nominee_category)) {
            case 'small':
                $criteria = BCriteria::where('bro_small', true)->count();
                break;
            case 'medium':
                $criteria = BCriteria::where('bro_medium', true)->count();
                break;
            case 'large':
                $criteria = BCriteria::where('bro_large', true)->count();
                break;
            default:
                $criteria = 0;
                break;
        }
    }

    // Calculate percentage safely
    $percentage = ($criteria > 0) ? ($data / $criteria) * 100 : 0;

    return $percentage;
}

    private function countCpercentage($nomineeId)
    {
        $data = BroScore::where('criteria_table', 'c_criterias')
            ->where('nominee_id', $nomineeId)
            ->count();
        $criteria = CCriteria::where('bro_small', true)
        ->where('bro_medium', true)
        ->where('bro_large', true)
        ->count();

        $percentage = ($data / $criteria) * 100;
        return $percentage;
    }
    private function countDpercentage($nomineeId)
    {
        $data = BroScore::where('criteria_table', 'd_criterias')
            ->where('nominee_id', $nomineeId)
            ->count();
        $criteria = DCriteria::where('bro_small', true)
        ->where('bro_medium', true)
        ->where('bro_large', true)
        ->count();

        $percentage = (($data / $criteria)/ 8) * 100;
        return $percentage;
    }
    private function countEpercentage($nomineeId)
    {
        $data = BroScore::where('criteria_table', 'e_criterias')
            ->where('nominee_id', $nomineeId)
            ->count();
        $criteria = ECriteria::where('bro_small', true)
        ->where('bro_medium', true)
        ->where('bro_large', true)
        ->count();

        $percentage = ($data / $criteria) * 100;
        return $percentage;
    }
    private function totalpercentage($nomineeId)
    {
        $a = $this->countApercentage($nomineeId);
        $b = $this->countBpercentage($nomineeId);
        $c = $this->countCpercentage($nomineeId);
        $d = $this->countDpercentage($nomineeId);
        $e = $this->countEpercentage($nomineeId);

        // Compute the average of the five percentages
        $percentage = ($a + $b + $c + $d + $e) / 5;

        return round($percentage, 2);
    }

    public function countApercentageGP($combinedEvaluationId)
{
    // Get actual count of secretariat scores for criteria 'A'
    $data = DB::connection('mysql_jd')
        ->table('secretariat_scores')
        ->where('combined_evaluation_id', $combinedEvaluationId)
        ->where('criteria_type', 'A')
        ->count();

    $nominee = DB::connection('mysql_jd')
        ->table('combined_evaluations as ce')
        ->join('nominees as n', 'ce.user_id', '=', 'n.user_id')
        ->where('ce.id', $combinedEvaluationId)
        ->select('n.nominee_category')
        ->first();
        $criteriaQuery = ACriteria::query();
        if ($nominee) {
            switch (strtolower($nominee->nominee_category)) {
                case 'small':
                    $criteriaQuery->where('gp_small', true);
                    break;
                case 'medium':
                    $criteriaQuery->where('gp_medium', true);
                    break;
                case 'large':
                    $criteriaQuery->where('gp_large', true);
                    break;
                default:
                    // If unknown category, make sure it returns 0 criteria
                    $criteriaQuery->whereRaw('1 = 0');
                    break;
            }
        }
        $criteria = $criteriaQuery->count();
        $data = $data-2;
    // Compute percentage
    $percentage = $criteria > 0 ? ($data / $criteria) * 100 : 0;

    // Return both the count and percentage
    return $percentage;
}

public function countBpercentageGP($combinedEvaluationId)
{
    // Get actual count of secretariat scores for criteria 'B'
    $data = DB::connection('mysql_jd')
        ->table('secretariat_scores')
        ->where('combined_evaluation_id', $combinedEvaluationId)
        ->where('criteria_type', 'B')
        ->count();
    // Step 2: Get nominee category using the combined evaluation
    $nominee = DB::connection('mysql_jd')
        ->table('combined_evaluations as ce')
        ->join('nominees as n', 'ce.user_id', '=', 'n.user_id')
        ->where('ce.id', $combinedEvaluationId)
        ->select('n.nominee_category')
        ->first();
        $criteriaQuery = BCriteria::query();
        if ($nominee) {
            switch (strtolower($nominee->nominee_category)) {
                case 'small':
                    $criteriaQuery->where('gp_small', true);
                    break;
                case 'medium':
                    $criteriaQuery->where('gp_medium', true);
                    break;
                case 'large':
                    $criteriaQuery->where('gp_large', true);
                    break;
                default:
                    // If unknown category, make sure it returns 0 criteria
                    $criteriaQuery->whereRaw('1 = 0');
                    break;
            }
        }
        $criteria = $criteriaQuery->count();
        $data = $data-1;
    // Compute percentage
    $percentage = $criteria > 0 ? ($data / $criteria) * 100 : 0;

    // Return both the count and percentage
    return $percentage;
}


    private function countCpercentageGP($combinedEvaluationId)
    {
        // Step 1: Get how many scores exist for this evaluation and criteria type C
        $data = DB::connection('mysql_jd')
            ->table('secretariat_scores')
            ->where('combined_evaluation_id', $combinedEvaluationId)
            ->where('criteria_type', 'C')
            ->count();

        // Step 2: Get nominee category using the combined evaluation
        $nominee = DB::connection('mysql_jd')
            ->table('combined_evaluations as ce')
            ->join('nominees as n', 'ce.user_id', '=', 'n.user_id')
            ->where('ce.id', $combinedEvaluationId)
            ->select('n.nominee_category')
            ->first();

        // Step 3: Determine which flag to count in CCriteria
        $criteriaQuery = CCriteria::query();
        if ($nominee) {
            switch (strtolower($nominee->nominee_category)) {
                case 'small':
                    $criteriaQuery->where('gp_small', true);
                    break;
                case 'medium':
                    $criteriaQuery->where('gp_medium', true);
                    break;
                case 'large':
                    $criteriaQuery->where('gp_large', true);
                    break;
                default:
                    // If unknown category, make sure it returns 0 criteria
                    $criteriaQuery->whereRaw('1 = 0');
                    break;
            }
        }
        $criteria = $criteriaQuery->count();
        // Step 4: Compute percentage
        $percentage = $criteria > 0 ? ($data / $criteria) * 100 : 0;   
        return $percentage;
    }

    private function countDpercentageGP($combinedEvaluationId)
    {
        // Step 1: Get how many scores exist for this evaluation and criteria type D
        $data = DB::connection('mysql_jd')
            ->table('secretariat_scores')
            ->where('combined_evaluation_id', $combinedEvaluationId)
            ->where('criteria_type', 'D')
            ->count();

        // Step 2: Get nominee category using the combined evaluation
        $nominee = DB::connection('mysql_jd')
            ->table('combined_evaluations as ce')
            ->join('nominees as n', 'ce.user_id', '=', 'n.user_id')
            ->where('ce.id', $combinedEvaluationId)
            ->select('n.nominee_category')
            ->first();

        // Step 3: Determine which flag to count in DCriteria
        $criteriaQuery = DCriteria::query();
        if ($nominee) {
            switch (strtolower($nominee->nominee_category)) {
                case 'small':
                    $criteriaQuery->where('gp_small', true);
                    break;
                case 'medium':
                    $criteriaQuery->where('gp_medium', true);
                    break;
                case 'large':
                    $criteriaQuery->where('gp_large', true);
                    break;
                default:
                    // If unknown category, make sure it returns 0 criteria
                    $criteriaQuery->whereRaw('1 = 0');
                    break;
            }
        }
        $criteria = $criteriaQuery->count();
        // Step 4: Compute percentage
        $percentage = $criteria > 0 ? ($data / $criteria) * 100 : 0;   
        return $percentage;
    }   

    private function countEpercentageGP($combinedEvaluationId)
    {
        // Step 1: Get how many scores exist for this evaluation and criteria type E
        $data = DB::connection('mysql_jd')
            ->table('secretariat_scores')
            ->where('combined_evaluation_id', $combinedEvaluationId)
            ->where('criteria_type', 'E')
            ->count();

        // Step 2: Get nominee category using the combined evaluation
        $nominee = DB::connection('mysql_jd')
            ->table('combined_evaluations as ce')
            ->join('nominees as n', 'ce.user_id', '=', 'n.user_id')
            ->where('ce.id', $combinedEvaluationId)
            ->select('n.nominee_category')
            ->first();

        // Step 3: Determine which flag to count in ECriteria
        $criteriaQuery = ECriteria::query();
        if ($nominee) {
            switch (strtolower($nominee->nominee_category)) {
                case 'small':
                    $criteriaQuery->where('gp_small', true);
                    break;
                case 'medium':
                    $criteriaQuery->where('gp_medium', true);
                    break;
                case 'large':
                    $criteriaQuery->where('gp_large', true);
                    break;
                default:
                    // If unknown category, make sure it returns 0 criteria
                    $criteriaQuery->whereRaw('1 = 0');
                    break;
            }
        }
        $criteria = $criteriaQuery->count();
        // Step 4: Compute percentage
        $percentage = $criteria > 0 ? ($data / $criteria) * 100 : 0;   
        return $percentage;
    }

    private function countApercentageTTI($combinedEvaluationId)
    {
        $data = DB::connection('mysql_jd')
            ->table('secretariat_scores')
            ->where('combined_evaluation_id', $combinedEvaluationId)
            ->where('criteria_type', 'A')
            ->count();

        $nominee = DB::connection('mysql_jd')
            ->table('combined_evaluations as ce')
            ->join('nominees as n', 'ce.user_id', '=', 'n.user_id')
            ->where('ce.id', $combinedEvaluationId)
            ->select('n.nominee_category')
            ->first();

        $criteriaQuery = ACriteria::query();
        if ($nominee) {
            switch (strtolower($nominee->nominee_category)) {
                case 'rtc-stc':
                    $criteriaQuery->where('bti_rtcstc', true);
                    break;
                case 'ptc-dtc':
                    $criteriaQuery->where('bti_ptcdtc', true);
                    break;
                case 'tas':
                    $criteriaQuery->where('bti_tas', true);
                    break;
                default:
                    // If unknown category, make sure it returns 0 criteria
                    $criteriaQuery->whereRaw('1 = 0');
                    break;
            }
        }

        $criteria = $criteriaQuery->count();

        switch (strtolower($nominee->nominee_category)) {
            case 'rtc-stc':
                $data = max(0, $data - 21);
                break;
            case 'ptc-dtc':
                $data = max(0, $data - 17);
                break;
            case 'tas':
                $data = max(0, $data - 21);
                break;
            default:
                // no adjustment for others
                break;
        }
        // Step 4: Compute percentage
        $percentage = $criteria > 0 ? ($data / $criteria) * 100 : 0;   
        return $percentage;
    }

    private function countBpercentageTTI($combinedEvaluationId)
    {
        $data = DB::connection('mysql_jd')
            ->table('secretariat_scores')
            ->where('combined_evaluation_id', $combinedEvaluationId)
            ->where('criteria_type', 'B')
            ->count();

        $nominee = DB::connection('mysql_jd')
            ->table('combined_evaluations as ce')
            ->join('nominees as n', 'ce.user_id', '=', 'n.user_id')
            ->where('ce.id', $combinedEvaluationId)
            ->select('n.nominee_category')
            ->first();

        $criteriaQuery = BCriteria::query();
        if ($nominee) {
            switch (strtolower($nominee->nominee_category)) {
                case 'rtc-stc':
                    $criteriaQuery->where('bti_rtcstc', true);
                    break;
                case 'ptc-dtc':
                    $criteriaQuery->where('bti_ptcdtc', true);
                    break;
                case 'tas':
                    $criteriaQuery->where('bti_tas', true);
                    break;
                default:
                    // If unknown category, make sure it returns 0 criteria
                    $criteriaQuery->whereRaw('1 = 0');
                    break;
            }
        }

        $criteria = $criteriaQuery->count();

        switch (strtolower($nominee->nominee_category)) {
            case 'rtc-stc':
                $criteria = max(0, $criteria + 1);
                break;
            case 'tas':
                $criteria = max(0, $criteria + 1);
                break;
            default:
                // no adjustment for others
                break;
        }
        // Step 4: Compute percentage
        $percentage = $criteria > 0 ? ($data / $criteria) * 100 : 0;   
        return $percentage;
    }

    private function countCpercentageTTI($combinedEvaluationId)
    {
        $data = DB::connection('mysql_jd')
            ->table('secretariat_scores')
            ->where('combined_evaluation_id', $combinedEvaluationId)
            ->where('criteria_type', 'C')
            ->count();

        $nominee = DB::connection('mysql_jd')
            ->table('combined_evaluations as ce')
            ->join('nominees as n', 'ce.user_id', '=', 'n.user_id')
            ->where('ce.id', $combinedEvaluationId)
            ->select('n.nominee_category')
            ->first();

        $criteriaQuery = CCriteria::query();
        if ($nominee) {
            switch (strtolower($nominee->nominee_category)) {
                case 'rtc-stc':
                    $criteriaQuery->where('bti_rtcstc', true);
                    break;
                case 'ptc-dtc':
                    $criteriaQuery->where('bti_ptcdtc', true);
                    break;
                case 'tas':
                    $criteriaQuery->where('bti_tas', true);
                    break;
                default:
                    // If unknown category, make sure it returns 0 criteria
                    $criteriaQuery->whereRaw('1 = 0');
                    break;
            }
        }

        $criteria = $criteriaQuery->count();
        // Step 4: Compute percentage
        $percentage = $criteria > 0 ? ($data / $criteria) * 100 : 0;   
        return $percentage;
    }

    private function countDpercentageTTI($combinedEvaluationId)
    {
        $data = DB::connection('mysql_jd')
            ->table('secretariat_scores')
            ->where('combined_evaluation_id', $combinedEvaluationId)
            ->where('criteria_type', 'D')
            ->count();

        $nominee = DB::connection('mysql_jd')
            ->table('combined_evaluations as ce')
            ->join('nominees as n', 'ce.user_id', '=', 'n.user_id')
            ->where('ce.id', $combinedEvaluationId)
            ->select('n.nominee_category')
            ->first();

        $criteriaQuery = DCriteria::query();
        if ($nominee) {
            switch (strtolower($nominee->nominee_category)) {
                case 'rtc-stc':
                    $criteriaQuery->where('bti_rtcstc', true);
                    break;
                case 'ptc-dtc':
                    $criteriaQuery->where('bti_ptcdtc', true);
                    break;
                case 'tas':
                    $criteriaQuery->where('bti_tas', true);
                    break;
                default:
                    // If unknown category, make sure it returns 0 criteria
                    $criteriaQuery->whereRaw('1 = 0');
                    break;
            }
        }

        $criteria = $criteriaQuery->count();
        // Step 4: Compute percentage
        $percentage = $criteria > 0 ? ($data / $criteria) * 100 : 0;   
        return $percentage;
    }

    private function countEpercentageTTI($combinedEvaluationId)
    {
        $data = DB::connection('mysql_jd')
            ->table('secretariat_scores')
            ->where('combined_evaluation_id', $combinedEvaluationId)
            ->where('criteria_type', 'E')
            ->count();

        $nominee = DB::connection('mysql_jd')
            ->table('combined_evaluations as ce')
            ->join('nominees as n', 'ce.user_id', '=', 'n.user_id')
            ->where('ce.id', $combinedEvaluationId)
            ->select('n.nominee_category')
            ->first();

        $criteriaQuery = ECriteria::query();
        if ($nominee) {
            switch (strtolower($nominee->nominee_category)) {
                case 'rtc-stc':
                    $criteriaQuery->where('bti_rtcstc', true);
                    break;
                case 'ptc-dtc':
                    $criteriaQuery->where('bti_ptcdtc', true);
                    break;
                case 'tas':
                    $criteriaQuery->where('bti_tas', true);
                    break;
                default:
                    // If unknown category, make sure it returns 0 criteria
                    $criteriaQuery->whereRaw('1 = 0');
                    break;
            }
        }

        $criteria = $criteriaQuery->count();
        // Step 4: Compute percentage
        $percentage = $criteria > 0 ? ($data / $criteria) * 100 : 0;   
        return $percentage;
    }

    private function countExternalCategoryPercentage($combinedEvaluationId, $criteriaType, $evaluatorId)
{
    // Step 1: Count filled scores for this evaluator & criteria type
    $filledCount = DB::connection('mysql_jd')
        ->table('external_validator_scores')
        ->where('combined_evaluation_id', $combinedEvaluationId)
        ->where('evaluator_id', $evaluatorId)
        ->where('criteria_type', $criteriaType)
        ->count();

    // Step 2: Get the nominee's category and type (GP / TI)
    $nominee = DB::connection('mysql_jd')
        ->table('combined_evaluations as ce')
        ->join('nominees as n', 'ce.user_id', '=', 'n.user_id')
        ->where('ce.id', $combinedEvaluationId)
        ->select('n.nominee_category', 'n.nominee_type')
        ->first();

    // Exclude BRO nominees
    if (!$nominee || !in_array(($nominee->nominee_type), ['GP', 'BTI'])) {
        return 0;
    }

    // Step 3: Select the proper Criteria model dynamically
    $criteriaModels = [
        'A' => ACriteria::class,
        'B' => BCriteria::class,
        'C' => CCriteria::class,
        'D' => DCriteria::class,
        'E' => ECriteria::class,
    ];

    if (!isset($criteriaModels[$criteriaType])) {
        return 0;
    }

    $model = $criteriaModels[$criteriaType];
    $criteriaQuery = $model::query();

    if ($nominee->nominee_type === 'BTI') {
        // 🟩 For BTI Nominees (Training Institutions)
        switch (strtolower($nominee->nominee_category)) {
            case 'rtc-stc':
                $criteriaQuery->where('bti_rtcstc', true);
                break;
            case 'ptc-dtc':
                $criteriaQuery->where('bti_ptcdtc', true);
                break;
            case 'tas':
                $criteriaQuery->where('bti_tas', true);
                break;
            default:
                $criteriaQuery->whereRaw('1 = 0'); // invalid category
                break;
        }
    } elseif ($nominee->nominee_type === 'GP') {
        // 🟦 For GP Nominees (Enterprises)
        switch (strtolower($nominee->nominee_category)) {
            case 'small':
                $criteriaQuery->where('gp_small', true);
                break;
            case 'medium':
                $criteriaQuery->where('gp_medium', true);
                break;
            case 'large':
                $criteriaQuery->where('gp_large', true);
                break;
            default:
                $criteriaQuery->whereRaw('1 = 0'); // invalid category
                break;
        }
    }


    $criteriaCount = $criteriaQuery->count();

    if (
        $criteriaType === 'B' &&
        $nominee->nominee_type === 'BTI' &&
        in_array(strtolower($nominee->nominee_category), ['rtc-stc', 'tas'])
    ) {
        $criteriaCount += 1;
    } elseif ( $criteriaType === 'B' &&
        $nominee->nominee_type === 'GP')
    {
        $criteriaCount += 1;
    }

    // Step 5: Compute percentage
    return $criteriaCount > 0 ? round(($filledCount / $criteriaCount) * 100, 2) : 0;
}



    


}
