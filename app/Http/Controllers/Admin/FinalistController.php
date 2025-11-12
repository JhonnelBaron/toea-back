<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\FinalistService;
use Illuminate\Http\Request;

class FinalistController extends Controller
{
    protected $finalistService;

    public function __construct(FinalistService $finalistService)
    {
        $this->finalistService = $finalistService;
    }

    public function get(Request $request)
    {
        $finalist = $this->finalistService->getFinalists($request);
        return response($finalist, $finalist['status']);
    }

    public function total()
    {
        $totalFinalists = $this->finalistService->totalFinalists(request());
        return response($totalFinalists, $totalFinalists['status']);
    }

    public function BroFinalists(Request $request)
    {
        $response = $this->finalistService->get($request);
        return response($response, $response['status']);
    }
}
