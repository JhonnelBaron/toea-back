<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\RopotiService;
use Illuminate\Http\Request;

class RopotiController extends Controller
{
    protected $ropotiService;

    public function __construct(RopotiService $ropotiService)
    {
         $this->ropotiService = $ropotiService;
    } 

    //Regions
    public function getRO()
    {
        $regions = $this->ropotiService->getRegions();
        return response($regions, $regions['status']);
    }

    public function addRO(Request $request)
    {
        $region = $this->ropotiService->addRegions($request->all());
        return response($region, $region['status']);
    }

    public function editRO(Request $request, $id)
    {
        $region = $this->ropotiService->updateRegion($id, $request->all());
        return response($region, $region['status']);
    }
    public function deleteRO($id)
    {
        $region = $this->ropotiService->deleteRegion($id);
        return response($region, $region['status']);
    }

    //Province
    public function getPO()
    {
        $province = $this->ropotiService->getProvinces();
        return response($province, $province['status']);
    }

    public function addPO(Request $request)
    {
        $province = $this->ropotiService->addProvince($request->all());
        return response($province, $province['status']);
    }

    public function editPO(Request $request, $id)
    {
        $province = $this->ropotiService->updateProvince($id,$request->all());
        return response($province, $province['status']);   
    }
    public function deletePO($id)
    {
        $province = $this->ropotiService->deleteProvince($id);
        return response($province, $province['status']);
    }

    // Institutions
    public function getTI()
    {
        $institutions = $this->ropotiService->getInstitutions();
        return response($institutions, $institutions['status']);
    }
    public function addTI(Request $request)
    {
        $institution = $this->ropotiService->addInstitution($request->all());
        return response($institution, $institution['status']);
    }
    public function editTI(Request $request, $id)
    {
        $institution = $this->ropotiService->updateInstitution($id,$request->all());
        return response($institution, $institution['status']);
    }
    public function deleteTI($id)
    {
        $institution = $this->ropotiService->deleteInstitution($id);
        return response($institution, $institution['status']);
    }
}
