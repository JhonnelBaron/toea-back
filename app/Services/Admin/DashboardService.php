<?php

namespace App\Services\Admin;

use App\Models\Nominee;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardService
{
    public function getNominees()
    {
        return [
            'status' => 200,
            'message' => 'Dashboard counts retrieved successfully.',
            'total_nominees' => Nominee::count(),
            'bro' => $this->getBroStats(),
            'gp' => $this->getGpStats(),
            'bti' => $this->getBtiStats(),
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
        $count = Nominee::where('nominee_type', 'GP')->count();

        $categories = Nominee::where('nominee_type', 'GP')
            ->selectRaw('nominee_category, COUNT(*) as total')
            ->groupBy('nominee_category')
            ->pluck('total', 'nominee_category');

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
        $count = Nominee::where('nominee_type', 'BTI')->count();

        $categories = Nominee::where('nominee_type', 'BTI')
            ->selectRaw('nominee_category, COUNT(*) as total')
            ->groupBy('nominee_category')
            ->pluck('total', 'nominee_category');

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
        return User::selectRaw('user_type, COUNT(*) as total')
            ->groupBy('user_type')
            ->pluck('total', 'user_type');
    }

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

    
}