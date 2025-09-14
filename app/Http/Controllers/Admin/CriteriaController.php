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
        $criteria = $this->criteriaService->update($request->validated(), $id);
        return response($criteria, $criteria['status']);
    }

    public function getCriteriaId($id)
    {
        $criteria = $this->criteriaService->getCriteriaId($id);
        return response($criteria, $criteria['status']);
    }

    public function TagsA(Request $request, $id)
    {
        // Expecting JSON like: { "fields": ["gp_small", "bti_tas"] }
        $fields = (array) $request->input('fields', []);


        $criteria = $this->criteriaService->BoolCriteriaA($id, $fields);

        return response($criteria, $criteria['status']);
    }

    public function ExecuteConfigA(Request $request, $id)
    {
        // Expecting JSON like: { "fields": ["gp_small", "bti_tas"] }
        $fields = (array) $request->input('fields', []);
        $criteria = $this->criteriaService->executiveConfigA($id, $fields);
        return response($criteria, $criteria['status']);
    }

    public function deleteA($id)
    {
        $criteria = $this->criteriaService->deleteCriteria($id);
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

    public function editB(CriteriaRequestB $request, $id)
    {
        $criteria = $this->criteriaService->updateB($request->validated(), $id);
        return response($criteria, $criteria['status']);
    }

    public function getBCriteriaId($id)
    {
        $criteria = $this->criteriaService->getBCriteriaId($id);
        return response($criteria, $criteria['status']);
    }
    
    public function TagsB(Request $request, $id)
    {
        // Expecting JSON like: { "fields": ["gp_small", "bti_tas"] }
        $fields = (array) $request->input('fields', []);
        $criteria = $this->criteriaService->BoolCriteriaB($id, $fields);
        return response($criteria, $criteria['status']);
    }

    public function ExecuteConfigB(Request $request, $id)
    {
        // Expecting JSON like: { "fields": ["gp_small", "bti_tas"] }
        $fields = (array) $request->input('fields', []);
        $criteria = $this->criteriaService->executiveConfigB($id, $fields);
        return response($criteria, $criteria['status']);
    }

    public function deleteB($id)
    {
        $criteria = $this->criteriaService->deleteCriteriaB($id);
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

    public function editC(CriteriaRequestC $request, $id)
    {
        $criteria = $this->criteriaService->updateC($request->validated(), $id);
        return response($criteria, $criteria['status']);
    }

    public function getCCriteriaId($id)
    {
        $criteria = $this->criteriaService->getCCriteriaId($id);
        return response($criteria, $criteria['status']);
    }

    public function TagsC(Request $request, $id)
    {
        // Expecting JSON like: { "fields": ["gp_small", "bti_tas"] }
        $fields = (array) $request->input('fields', []);
        $criteria = $this->criteriaService->BoolCriteriaC($id, $fields);
        return response($criteria, $criteria['status']);
    }

    public function ExecuteConfigC(Request $request, $id)
    {
        // Expecting JSON like: { "fields": ["gp_small", "bti_tas"] }
        $fields = (array) $request->input('fields', []);
        $criteria = $this->criteriaService->executiveConfigC($id, $fields);
        return response($criteria, $criteria['status']);
    }

    public function deleteC($id)
    {
        $criteria = $this->criteriaService->deleteCriteriaC($id);
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

    public function editD(CriteriaRequestD $request, $id)
    {
        $criteria = $this->criteriaService->updateD($request->all(), $id);
        return response($criteria, $criteria['status']);
    }

    public function getDCriteriaId($id)
    {
        $criteria = $this->criteriaService->getDCriteriaId($id);
        return response($criteria, $criteria['status']);
    }

    public function TagsD(Request $request, $id)
    {
        // Expecting JSON like: { "fields": ["gp_small", "bti_tas"] }
        $fields = (array) $request->input('fields', []);
        $criteria = $this->criteriaService->BoolCriteriaD($id, $fields);
        return response($criteria, $criteria['status']);
    }

    public function ExecuteConfigD(Request $request, $id)
    {
        // Expecting JSON like: { "fields": ["gp_small", "bti_tas"] }
        $fields = (array) $request->input('fields', []);
        $criteria = $this->criteriaService->executiveConfigD($id, $fields);
        return response($criteria, $criteria['status']);
    }

    public function deleteD($id)
    {
        $criteria = $this->criteriaService->deleteCriteriaD($id);
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
    public function editE(CriteriaRequestE $request, $id)
    {
        $criteria = $this->criteriaService->updateE($request->all(), $id);
        return response($criteria, $criteria['status']);
    }
    public function getECriteriaId($id)
    {
        $criteria = $this->criteriaService->getECriteriaId($id);
        return response($criteria, $criteria['status']);
    }

    public function TagsE(Request $request, $id)
    {
        // Expecting JSON like: { "fields": ["gp_small", "bti_tas"] }
        $fields = (array) $request->input('fields', []);
        $criteria = $this->criteriaService->BoolCriteriaE($id, $fields);
        return response($criteria, $criteria['status']);
    }

    public function ExecuteConfigE(Request $request, $id)
    {
        // Expecting JSON like: { "fields": ["gp_small", "bti_tas"] }
        $fields = (array) $request->input('fields', []);
        $criteria = $this->criteriaService->executiveConfigE($id, $fields);
        return response($criteria, $criteria['status']);
    }

    public function deleteE($id)
    {
        $criteria = $this->criteriaService->deleteCriteriaE($id);
        return response($criteria, $criteria['status']);
    }

}
