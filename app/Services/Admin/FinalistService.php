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
    public function getFinalists()
    {
        // Call the private helper functions
        $broFinalists = $this->getBroFinalist();
        $gpFinalists = $this->getGpFinalist();
        $ttiFinalists = $this->getTtiFinalist();

        // Combine or structure them by category/type
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

    public function totalFinalists()
    {
        // Get the count of all finalists
        $broFinalists = $this->getBroFinalist();
        $gpFinalists = $this->getGpFinalist();
        $ttiFinalists = $this->getTtiFinalist();

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

    private function getGpFinalist()
    {
        $gpFinalists = DB::connection('mysql_jd')
            ->table('combined_evaluations as ce')
            ->join('nominees as n', 'ce.user_id', '=', 'n.user_id')
            ->where('ce.status', 'endorsed_to_external_validator')
            ->where('n.nominee_type', 'GP')
            ->select(
                'ce.user_id',
                'n.nominee_name',
                'n.nominee_category'
            )
            ->distinct() // optional: to avoid duplicates if same user_id appears multiple times
            ->get();

        return $gpFinalists;
    }    
        private function getTtiFinalist()
    {
        $ttiFinalists = DB::connection('mysql_jd')
            ->table('combined_evaluations as ce')
            ->join('nominees as n', 'ce.user_id', '=', 'n.user_id')
            ->where('ce.status', 'endorsed_to_external_validator')
            ->where('n.nominee_type', 'BTI')
            ->select(
                'ce.user_id',
                'n.nominee_name',
                'n.nominee_category',
            )
            ->distinct() // optional: to avoid duplicates if same user_id appears multiple times
            ->get();

        return $ttiFinalists;
    }

    // private function getGpFinalist()
    // {
    //     $gpFinalists = DB::connection('mysql')
    //         ->table('u782169281_test_jd.combined_evaluations as ce')
    //         ->join('u782169281_test_jd.nominees as n', 'ce.user_id', '=', 'n.user_id')
    //         ->where('ce.status', 'endorsed_to_external_validator')
    //         ->where('n.nominee_type', 'GP')
    //         ->select(
    //             'ce.user_id',
    //             'n.nominee_name',
    //             'n.nominee_category'
    //         )
    //         ->distinct() // optional: to avoid duplicates if same user_id appears multiple times
    //         ->get();

    //     return $gpFinalists;
    // }

    // private function getTtiFinalist()
    // {
    //     $ttiFinalists = DB::connection('mysql')
    //         ->table('u782169281_test_jd.combined_evaluations as ce')
    //         ->join('u782169281_test_jd.nominees as n', 'ce.user_id', '=', 'n.user_id')
    //         ->where('ce.status', 'endorsed_to_external_validator')
    //         ->where('n.nominee_type', 'BTI')
    //         ->select(
    //             'ce.user_id',
    //             'n.nominee_name',
    //             'n.nominee_category',
    //         )
    //         ->distinct() // optional: to avoid duplicates if same user_id appears multiple times
    //         ->get();

    //     return $ttiFinalists;
    // }


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
        }

        return [
            'status' => 200,
            'message' => 'BRO summaries retrieved successfully.',
            'data' => $data,
        ];
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



}
