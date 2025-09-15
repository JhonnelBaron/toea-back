<?php

namespace App\Http\Controllers\Executive;

use App\Http\Controllers\Controller;
use App\Services\Executive\EvaluationService;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class EvaluationController extends Controller
{
    protected $evaluationService;

    public function __construct(EvaluationService $evaluationService)
    {
        $this->evaluationService = $evaluationService;
    }

    public function index()
    {
        $user = JWTAuth::user();

        $criterias = $this->evaluationService->getCriteriaForOffice($user->office);

        return response()->json($criterias);
    }

     public function getACriteria()
    {
        $user = JWTAuth::user();
        $office = $user->office ?? null;

        return response()->json(
            $this->evaluationService->getACriteriaForOffice($office)
        );
    }

    public function getBCriteria()
    {
        $user = JWTAuth::user();
        $office = $user->office ?? null;

        return response()->json(
            $this->evaluationService->getBCriteriaForOffice($office)
        );
    }

    public function getCCriteria()
    {
        $user = JWTAuth::user();
        $office = $user->office ?? null;

        return response()->json(
            $this->evaluationService->getCCriteriaForOffice($office)
        );
    }

    public function getDCriteria()
    {
        $user = JWTAuth::user();
        $office = $user->office ?? null;

        return response()->json(
            $this->evaluationService->getDCriteriaForOffice($office)
        );
    }

    public function getECriteria()
    {
        $user = JWTAuth::user();
        $office = $user->office ?? null;

        return response()->json(
            $this->evaluationService->getECriteriaForOffice($office)
        );
    }
}
