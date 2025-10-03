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

    public function get($id)
    {
        $nominee = $this->evaluationService->get($id);
        return response($nominee, $nominee['status']);
    }

    public function store(Request $request, EvaluationService $evaluationService)
    {
        $validated = $request->validate([
            'nominee_id'     => 'required|exists:nominees,id',
            'score'          => 'required|numeric|min:0',
            'remarks'        => 'nullable|string',
            'criteria_table' => 'required|string|in:a_criterias,b_criterias,c_criterias,d_criterias,e_criterias',
            'criteria_id'    => 'required|integer',
            'attachment'     => 'nullable|file|max:20480', // optional file
        ]);

        // include file if uploaded
        if ($request->hasFile('attachment')) {
            $validated['attachment'] = $request->file('attachment');
        }

        $score = $evaluationService->addScore($validated);

        return response()->json([
            'status'  => 201,
            'message' => 'Score recorded successfully.',
            'data'    => $score
        ]);
    }

    public function showScore($id)
    {
        $score = $this->evaluationService->getScore($id);
        return response($score, $score['status']);
    }

    public function getScores($id)
    {
        $nominee = $this->evaluationService->getScoresForNominee($id);
        return response($nominee, $nominee['status']);
    }

    public function markAsDone($nomineeId)
    {
        $result = $this->evaluationService->markAsDone($nomineeId);
        return response($result, $result['status']);
    }

    public function getStatus($nomineeId)
    {
        $result = $this->evaluationService->getStatus($nomineeId);
        return response($result, $result['status']);
    }

    public function completionRate(Request $request)
    {
        $result = $this->evaluationService->updateAggregate($request->all());
        return response($result, $result['status']);
    }
}
