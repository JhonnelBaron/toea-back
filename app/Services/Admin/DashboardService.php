<?php

namespace App\Services\Admin;

use App\Models\ACriteria;
use App\Models\BCriteria;
use App\Models\CCriteria;
use App\Models\DCriteria;
use App\Models\ECriteria;
use App\Models\Evaluation\BroScore;
use App\Models\Evaluation\BroSummary;
use App\Models\Evaluation\ExecutiveScore;
use App\Models\Nominee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getNominees()
    {
        $bro = $this->getBroStats();
        $gp = $this->getGpStats();
        $bti = $this->getBtiStats();

        // 🔹 Add all nominee totals together (from both main and external if your functions handle them)
        $totalNominees = ($bro['count'] ?? 0) + ($gp['count'] ?? 0) + ($bti['count'] ?? 0);

        return [
            'status' => 200,
            'message' => 'Dashboard counts retrieved successfully.',
            'total_nominees' => $totalNominees,
            'bro' => $bro,
            'gp' => $gp,
            'bti' => $bti,
            'users_by_type' => $this->getUserStats(),
        ];
    }

    /**
     * BRO nominees with category breakdown
     */
    private function getBroStats()
    {
        $count = Nominee::where('nominee_type', 'BRO')->count();

        $categories = Nominee::where('nominee_type', 'BRO')
            ->selectRaw('nominee_category, COUNT(*) as total')
            ->groupBy('nominee_category')
            ->pluck('total', 'nominee_category');

        return [
            'count' => $count,
            'categories' => $categories,
        ];
    }

    /**
     * GP nominees with category breakdown
     */
    private function getGpStats()
    {
        $count = DB::connection('mysql_jd') // or your connection name
        ->table('nominees')
        ->where('nominee_type', 'GP')
        ->count();

        $categories = DB::connection('mysql_jd')
            ->table('nominees')
            ->where('nominee_type', 'GP')
            ->selectRaw('nominee_category, COUNT(*) as total')
            ->groupBy('nominee_category')
            ->pluck('total', 'nominee_category');
        
        $count = max(0, $count - 1);

        return [
            'count' => $count,
            'categories' => $categories,
        ];
    }

    /**
     * BTI nominees with category breakdown
     */
    private function getBtiStats()
    {
        $count = DB::connection('mysql_jd') // or your connection name
        ->table('nominees')
        ->where('nominee_type', 'BTI')
        ->count();

        $categories = DB::connection('mysql_jd')
            ->table('nominees')
            ->where('nominee_type', 'BTI')
            ->selectRaw('nominee_category, COUNT(*) as total')
            ->groupBy('nominee_category')
            ->pluck('total', 'nominee_category');

        $count = max(0, $count - 1);

        return [
            'count' => $count,
            'categories' => $categories,
        ];
    }

    /**
     * Users by type (secretariat, external validator, executive office focal, etc.)
     */
    private function getUserStats()
    {
        // 🔹 Count users from your main Laravel database
        $mainData = User::selectRaw('user_type, COUNT(*) as total')
            ->groupBy('user_type')
            ->pluck('total', 'user_type');

        // 🔹 Count users from the other database (u782169281_test_jd.users)
        $externalData = DB::connection('mysql_jd') // same connection, just specify DB name
            ->table('users')
            ->selectRaw('user_type, COUNT(*) as total')
            ->groupBy('user_type')
            ->pluck('total', 'user_type');

        // 🔹 Merge both counts (adding totals where user_type matches)
        $combined = $mainData->mergeRecursive($externalData)->map(function ($item) {
            // handle cases where both sources have the same user_type
            return is_array($item) ? array_sum($item) : $item;
        });

        return [
            'status' => 200,
            'message' => 'User stats retrieved successfully.',
            'data' => $combined
        ];
    }

    // private function getGpStats()
    // {
    //     $count = DB::connection('mysql') // or your connection name
    //     ->table('u782169281_test_jd.nominees')
    //     ->where('nominee_type', 'GP')
    //     ->count();

    //     $categories = DB::connection('mysql')
    //         ->table('u782169281_test_jd.nominees')
    //         ->where('nominee_type', 'GP')
    //         ->selectRaw('nominee_category, COUNT(*) as total')
    //         ->groupBy('nominee_category')
    //         ->pluck('total', 'nominee_category');
        
    //     $count = max(0, $count - 1);

    //     return [
    //         'count' => $count,
    //         'categories' => $categories,
    //     ];
    // }

    // /**
    //  * BTI nominees with category breakdown
    //  */
    // private function getBtiStats()
    // {
    //     $count = DB::connection('mysql') // or your connection name
    //     ->table('u782169281_test_jd.nominees')
    //     ->where('nominee_type', 'BTI')
    //     ->count();

    //     $categories = DB::connection('mysql')
    //         ->table('u782169281_test_jd.nominees')
    //         ->where('nominee_type', 'BTI')
    //         ->selectRaw('nominee_category, COUNT(*) as total')
    //         ->groupBy('nominee_category')
    //         ->pluck('total', 'nominee_category');

    //     $count = max(0, $count - 1);

    //     return [
    //         'count' => $count,
    //         'categories' => $categories,
    //     ];
    // }

    // /**
    //  * Users by type (secretariat, external validator, executive office focal, etc.)
    //  */
    // private function getUserStats()
    // {
    //     // 🔹 Count users from your main Laravel database
    //     $mainData = User::selectRaw('user_type, COUNT(*) as total')
    //         ->groupBy('user_type')
    //         ->pluck('total', 'user_type');

    //     // 🔹 Count users from the other database (u782169281_test_jd.users)
    //     $externalData = DB::connection('mysql') // same connection, just specify DB name
    //         ->table('u782169281_test_jd.users')
    //         ->selectRaw('user_type, COUNT(*) as total')
    //         ->groupBy('user_type')
    //         ->pluck('total', 'user_type');

    //     // 🔹 Merge both counts (adding totals where user_type matches)
    //     $combined = $mainData->mergeRecursive($externalData)->map(function ($item) {
    //         // handle cases where both sources have the same user_type
    //         return is_array($item) ? array_sum($item) : $item;
    //     });

    //     return [
    //         'status' => 200,
    //         'message' => 'User stats retrieved successfully.',
    //         'data' => $combined
    //     ];
    // }


    public function getUsers(Request $request)
    {
        $type = $request->query('type'); // get ?type=secretariat from the URL

        $query = User::query();

        if ($type) {
            $query->where('user_type', $type);
        }

        $users = $query->get();

        return [
            'status' => 200,
            'message' => $type 
                ? "Users with type '{$type}' retrieved successfully."
                : 'All users retrieved successfully.',
            'data' => $users
        ];
    }

    public function getRatings($id)
    {
        $progress = ExecutiveScore::with(['nominee']) // eager load nominee relation
            ->where('user_id', $id)
            ->get(['nominee_id','total_score','overall_score','completion_rate']);

        return [
            'status' => 200,
            'message' => 'Score Rating retrieved successfully.',
            'user_id' => $id,
            'overall_completion_rate' => User::find($id)->overall_completion_rate ?? 0,
            'data' => $progress
        ];
    }

    public function getMonitoring()
    {
        $data = ExecutiveScore::with(['user', 'nominee'])
            ->get();

    }

        private function fetchBroCriteria($modelClass, $relationship)
    {
        $criterias = $modelClass::with($relationship)
            ->where(function ($query) {
                $query->where('bro_small', true)
                    ->orWhere('bro_medium', true)
                    ->orWhere('bro_large', true);
            })
            ->get();

        if ($criterias->isEmpty()) {
            return [
                'status' => 404,
                'message' => 'No BRO criteria found.',
                'data' => [],
            ];
        }

        return [
            'status' => 200,
            'message' => 'BRO criteria retrieved successfully.',
            'data' => $criterias,
        ];
    }

    // A to E criteria for BRO
    public function getBroCriteriaA()
    {
        return $this->fetchBroCriteria(ACriteria::class, 'aRequirements');
    }

    public function getBroCriteriaB()
    {
        return $this->fetchBroCriteria(BCriteria::class, 'bRequirements');
    }

    public function getBroCriteriaC()
    {
        return $this->fetchBroCriteria(CCriteria::class, 'cRequirements');
    }

    public function getBroCriteriaD()
    {
        return $this->fetchBroCriteria(DCriteria::class, 'dRequirements');
    }

    public function getBroCriteriaE()
    {
        return $this->fetchBroCriteria(ECriteria::class, 'eRequirements');
    }

     public function getScoresByCriteriaTable($nomineeId, $criteriaTable)
    {
        $scores = BroScore::where('nominee_id', $nomineeId)
            ->where('criteria_table', $criteriaTable)
            ->select('criteria_id', 'user_id', 'score', 'remarks', 'attachment_path', 'attachment_name', 'attachment_type', 'criteria_table')
            ->get();

        return [
            'status' => 200,
            'message' => 'Scores retrieved successfully.',
            'data' => $scores,
        ];
    }

    // Each criteria section (A–E)
    public function getACriterias($nomineeId)
    {
        return $this->getScoresByCriteriaTable($nomineeId, 'a_criterias');
    }

    public function getBCriterias($nomineeId)
    {
        return $this->getScoresByCriteriaTable($nomineeId, 'b_criterias');
    }

    public function getCCriterias($nomineeId)
    {
        return $this->getScoresByCriteriaTable($nomineeId, 'c_criterias');
    }

    public function getDCriterias($nomineeId)
    {
        return $this->getScoresByCriteriaTable($nomineeId, 'd_criterias');
    }

    public function getECriterias($nomineeId)
    {
        return $this->getScoresByCriteriaTable($nomineeId, 'e_criterias');
    }

    public function get(Request $request)
    {
        $query = BroSummary::with('nominee');

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
        $percentage = ($data / 10) * 100;
        return $percentage;
    }
    private function countBpercentage($nomineeId)
    {
        $data = BroScore::where('criteria_table', 'b_criterias')
            ->where('nominee_id', $nomineeId)
            ->count();
        $percentage = ($data / 50) * 100;
        return $percentage;
    }
    private function countCpercentage($nomineeId)
    {
        $data = BroScore::where('criteria_table', 'c_criterias')
            ->where('nominee_id', $nomineeId)
            ->count();
        $percentage = ($data / 12) * 100;
        return $percentage;
    }
    private function countDpercentage($nomineeId)
    {
        $data = BroScore::where('criteria_table', 'd_criterias')
            ->where('nominee_id', $nomineeId)
            ->count();
        $percentage = ($data / 9) * 100;
        return $percentage;
    }
    private function countEpercentage($nomineeId)
    {
        $data = BroScore::where('criteria_table', 'e_criterias')
            ->where('nominee_id', $nomineeId)
            ->count();
        $percentage = ($data / 1) * 100;
        return $percentage;
    }
    private function totalpercentage($nomineeId)
    {
        $data = BroScore::where('nominee_id', $nomineeId)->count();
        $percentage = ($data / 82) * 100;
        return $percentage;
    }

    public function getPercentages($nomineeId)
    {
        return [
            'status' => 200,
            'message' => 'Criteria percentages retrieved successfully.',
            'data' => [
                'A_percentage' => $this->countApercentage($nomineeId),
                'B_percentage' => $this->countBpercentage($nomineeId),
                'C_percentage' => $this->countCpercentage($nomineeId),
                'D_percentage' => $this->countDpercentage($nomineeId),
                'E_percentage' => $this->countEpercentage($nomineeId),
                'total_percentage' => $this->totalpercentage($nomineeId),
            ],
        ];
    }

    public function endorseFinalist($nomineeId)
    {
        $nominee = BroSummary::where('nominee_id', $nomineeId)->first();

        if (!$nominee) {
            return response()->json([
                'status' => 404,
                'message' => 'Nominee not found.',
            ], 404);
        }

        // Flip the boolean value
        $nominee->endorse_externals = !$nominee->endorse_externals;
        $nominee->save();

        return [
            'status' => 200,
            'message' => $nominee->endorse_externals
                ? 'Nominee endorsed successfully.'
                : 'Nominee endorsement removed.',
            'data' => $nominee,
        ];
    }


    
}
