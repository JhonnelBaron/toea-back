<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CriteriaRequest;
use App\Services\Admin\CriteriaService;
use Illuminate\Http\Request;

class CriteriaController extends Controller
{
    protected $criteriaService;

    public function __construct(CriteriaService $criteriaService)
    {
        $this->criteriaService = $criteriaService;
    }

    public function store(CriteriaRequest $request)
    {
        $criteria = $this->criteriaService->create($request->validated());
        return response($criteria, $criteria['status']);
    }

    public function edit(CriteriaRequest $request, $id)
    {
        $criteria = $this->criteriaService->update($id, $request->validated());
        return response($criteria, $criteria['status']);
    }

}
