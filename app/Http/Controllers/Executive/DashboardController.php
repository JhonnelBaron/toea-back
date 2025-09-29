<?php

namespace App\Http\Controllers\Executive;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Executive\DashboardService;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function getBroNominees()
    {
        $response = $this->dashboardService->getBroNominees();
        return response($response, $response['status']);
    }

    public function getScoreRating()
    {
        $response = $this->dashboardService->getScoreRating();
        return response($response, $response['status']);
    }
}
