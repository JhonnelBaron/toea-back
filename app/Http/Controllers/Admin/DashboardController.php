<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function getCounts()
    {
        $response = $this->dashboardService->getNominees();
        return response($response, $response['status']);
    }

    public function getUsers(Request $request)
    {
        $response = $this->dashboardService->getUsers($request);
        return response($response, $response['status']);
    }
}
