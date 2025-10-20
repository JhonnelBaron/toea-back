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

    public function getRatings($id)
    {
        $response = $this->dashboardService->getRatings($id);
        return response($response, $response['status']);
    }

    public function getBroCriteriaA()
    {
        $response = $this->dashboardService->getBroCriteriaA();
        return response($response, $response['status']);
    }
        public function getBroCriteriaB()
    {
        $response = $this->dashboardService->getBroCriteriaB();
        return response($response, $response['status']);
    }

        public function getBroCriteriaC()
    {
        $response = $this->dashboardService->getBroCriteriaC();
        return response($response, $response['status']);
    }
        public function getBroCriteriaD()
    {
        $response = $this->dashboardService->getBroCriteriaD();
        return response($response, $response['status']);
    }
        public function getBroCriteriaE()
    {
        $response = $this->dashboardService->getBroCriteriaE();
        return response($response, $response['status']);
    }
    
    public function getScoresA($nomineeId)
    {
        $response = $this->dashboardService->getACriterias($nomineeId);
        return response($response, $response['status']);
    }

    public function getScoresB($nomineeId)
    {
        $response = $this->dashboardService->getBCriterias($nomineeId);
        return response($response, $response['status']);
    }

    public function getScoresC($nomineeId)
    {
        $response = $this->dashboardService->getCCriterias($nomineeId);
        return response($response, $response['status']);
    }
    public function getScoresD($nomineeId)
    {
        $response = $this->dashboardService->getDCriterias($nomineeId);
        return response($response, $response['status']);
    }
    public function getScoresE($nomineeId)
    {
        $response = $this->dashboardService->getECriterias($nomineeId);
        return response($response, $response['status']);
    }

}
