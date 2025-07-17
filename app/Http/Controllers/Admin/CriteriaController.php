<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CriteriaRequest;
use App\Http\Requests\Admin\CriteriaRequestB;
use App\Http\Requests\Admin\CriteriaRequestC;
use App\Http\Requests\Admin\CriteriaRequestD;
use App\Http\Requests\Admin\CriteriaRequestE;
use App\Services\Admin\CriteriaService;
use Illuminate\Http\Request;

class CriteriaController extends Controller
{
    protected $criteriaService;

    public function __construct(CriteriaService $criteriaService)
    {
        $this->criteriaService = $criteriaService;
    }

    //CRITERIA A
    public function getAll()
    {
        $criteria = $this->criteriaService->getAllCriterias();
        return response($criteria, $criteria['status']);
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

    //CRITERIA B
    public function getAllB()
    {
        $criteria = $this->criteriaService->getAllBCriterias();
        return response($criteria, $criteria['status']); 
    }

    public function storeB(CriteriaRequestB $request)
    {
        $criteria = $this->criteriaService->createB($request->validated());
        return response($criteria, $criteria['status']);
    }
    
    //CRITERIA C
    public function getAllC()
    {
        $criteria = $this->criteriaService->getAllCCriterias();
        return response($criteria, $criteria['status']);    
    }
    
    public function storeC(CriteriaRequestC $request)
    {
        $criteria = $this->criteriaService->createC($request->validated());
        return response($criteria, $criteria['status']);
    }

    //CRITERIA D
    public function getAllD()
    {
        $criteria = $this->criteriaService->getAllDCriterias();
        return response($criteria, $criteria['status']); 
    }

    public function storeD(CriteriaRequestD $request)
    {
        $criteria = $this->criteriaService->createD($request->all());
        return response($criteria, $criteria['status']);
    }

    //CRITERIA E
    public function getAllE()
    {
        $criteria = $this->criteriaService->getAllECriterias();
        return response($criteria, $criteria['status']);
    }
    public function storeE(CriteriaRequestE $request)
    {
        $criteria = $this->criteriaService->createE($request->all());
        return response($criteria, $criteria['status']);
    }


}
