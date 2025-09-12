<?php

namespace App\Services\Admin;

use App\Models\Config\Institution;
use App\Models\Config\Province;
use App\Models\Config\Region;

class RopotiService
{
    public function getRegions()
    {
        $regions = Region::all();

        return [
            'status' => 200,
            'message' => 'Regions retrieved successfully.',
            'data' => $regions
        ];
    }

    public function addRegions($data)
    {
        $region = Region::create($data);

        return [
            'status' => 201,
            'message' => 'Region created successfully.',
            'data' => $region
        ];
    }

    public function updateRegion($id, $data)
    {
        $region = Region::find($id);
        if (!$region) {
            return [
                'status' => 404,
                'message' => 'Region not found.',
            ];
        }

        $region->update($data);

        return [
            'status' => 200,
            'message' => 'Region updated successfully.',
            'data' => $region
        ];
    }

    public function deleteRegion($id)
    {
        $region = Region::find($id);
        if (!$region) {
            return [
                'status' => 404,
                'message' => 'Region not found.',
            ];
        }

        $region->delete();

        return [
            'status' => 200,
            'message' => 'Region deleted successfully.',
        ];
    }

    // Province
    public function getProvinces()
    {
        $provinces = Province::with('region')->get();

        return [
            'status' => 200,
            'message' => 'Provinces retrieved successfully.',
            'data' => $provinces
        ];
    }

    public function addProvince($data)
    {
        $region = Region::find($data['region_id']);
        if (!$region) {
            return [
                'status' => 404,
                'message' => 'Region not found.',
            ];
        }

        $province = Province::create([
            'region_id' => $data['region_id'],
            'name' => $data['name'],
        ]);

        return [
            'status' => 201,
            'message' => 'Province created successfully.',
            'data' => [
                'id' => $province->id,
                'name' => $province->name,
                'region_id' => $province->region_id,
                'region_name' => $province->region->name ?? null
            ]
        ];
    }

    public function updateProvince($id, $data)
    {
        $province = Province::find($id);
        if (!$province) {
            return [
                'status' => 404,
                'message' => 'Province not found.',
            ];
        }

        if (isset($data['region_id'])) {
            $region = Region::find($data['region_id']);
            if (!$region) {
                return [
                    'status' => 404,
                    'message' => 'Region not found.',
                ];
            }
        }

        $province->update($data);

        return [
            'status' => 200,
            'message' => 'Province updated successfully.',
            'data' => [
                'id' => $province->id,
                'name' => $province->name,
                'region_id' => $province->region_id,
                'region_name' => $province->region->name ?? null
            ]
        ];
    }

    public function deleteProvince($id)
    {
        $province = Province::find($id);
        if (!$province)
        {
            return [
                'status' => 404,
                'message' => 'Provicnce not found.',
            ];
        }

        $province->delete();

        return [
            'status' => 200,
            'message' => 'Province deleted successfully.',
        ];
    }

    // Training Institutions
    public function getInstitutions()
    {
        $institutions = Institution::with('province.region')->get();

        return [
            'status' => 200,
            'message' => 'Institutions retrieved successfully.',
            'data' => $institutions
        ];
    }

    public function addInstitution($data)
    {
        $province = Province::find($data['province_id']);
        if (!$province) {
            return [
                'status' => 404,
                'message' => 'Province not found.',
            ];
        }

        $institution = Institution::create([
            'province_id' => $data['province_id'],
            'name' => $data['name'],
        ]);

        return [
            'status' => 201,
            'message' => 'Institution created successfully.',
            'data' => [
                'id' => $institution->id,
                'name' => $institution->name,
                'province_id' => $institution->province_id,
                'province_name' => $institution->province->name ?? null,
                'region_name' => $institution->province->region->name ?? null,
            ]
        ];
    }
    public function updateInstitution($id, $data)
    {
        $institution = Institution::find($id);
        if (!$institution) {
            return [
                'status' => 404,
                'message' => 'Institution not found.',
            ];
        }

        if (isset($data['province_id'])) {
            $province = Province::find($data['province_id']);
            if (!$province) {
                return [
                    'status' => 404,
                    'message' => 'Province not found.',
                ];
            }
        }

        $institution->update($data);

        return [
            'status' => 200,
            'message' => 'Institution updated successfully.',
            'data' => [
                'id' => $institution->id,
                'name' => $institution->name,
                'province_id' => $institution->province_id,
                'province_name' => $institution->province->name ?? null,
                'region_name' => $institution->province->region->name ?? null,
            ]
        ];
    }
    public function deleteInstitution($id)
    {
        $institution = Institution::find($id);
        if (!$institution) {
            return [
                'status' => 404,
                'message' => 'Institution not found.',
            ];
        }

        $institution->delete();

        return [
            'status' => 200,
            'message' => 'Institution deleted successfully.',
        ];
    }


}