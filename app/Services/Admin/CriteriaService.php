<?php

namespace App\Services\Admin;

use App\Models\ACriteria;
use App\Models\BCriteria;
use App\Models\CCriteria;
use App\Models\DCriteria;
use App\Models\ECriteria;
use App\Models\Requirements\ARequirement;
use App\Models\Requirements\BRequirement;
use App\Models\Requirements\CRequirement;
use App\Models\Requirements\DRequirement;
use App\Models\Requirements\ERequirement;

class CriteriaService
{
    /**
     * Get all criteria.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */

    //CRITERIA A
    public function getAllCriterias()
    {
        $criteria = ACriteria::with('aRequirements')->get();

        return [
            'status' => 200,
            'message' => 'Criteria retrieved successfully.',
            'data' => $criteria
        ];
    }

    /**
     * Create a new criteria.
     *
     * @param array $data
     * @return \App\Models\ACriteria
     */
    public function create(array $data)
    {
            $requirements = $data['aRequirements'] ?? [];
    unset($data['aRequirements']);

        $criteria = ACriteria::create($data);

    // Loop through the extracted requirements
    if (!empty($requirements) && is_array($requirements)) {
        foreach ($requirements as $requirement) {
            if (!empty($requirement['requirement_description']) || isset($requirement['point_value'])) {
                ARequirement::create([
                    'a_criteria_id' => $criteria->id,
                    'requirement_description' => $requirement['requirement_description'] ?? null,
                    'point_value' => $requirement['point_value'] ?? null,
                ]);
            }
        }
    }


        return [
            'status' => 201,
            'message' => 'Criteria created successfully.',
            'data' => $criteria->load('aRequirements')
        ];
    }
  
    public function update( int $id, array $data,)
    {
        $criteria = ACriteria::findOrFail($id);
        $criteria->update($data);
        return [
            'data' => $criteria,
            'message' => 'Criteria Updated Successfully',
            'status' => 200
        ];
    }

    /**
     * Delete a criteria.
     *
     * @param int $id
     * @return void
     */
    public function deleteCriteria(int $id)
    {
        ACriteria::destroy($id);
    }

    // CRITERIA B
    public function getAllBCriterias()
    {
        $criteria = BCriteria::with('bRequirements')->get();

        return [
            'status' => 200,
            'message' => 'Criteria retrieved successfully.',
            'data' => $criteria
        ];
    }

    public function createB(array $data)
    {
        $requirements = $data['bRequirements'] ?? [];
        unset($data['bRequirements']);

        $criteria = BCriteria::create($data);

        // Loop through the extracted requirements
        if (!empty($requirements) && is_array($requirements)) {
            foreach ($requirements as $requirement) {
                if (!empty($requirement['requirement_description']) || isset($requirement['point_value'])) {
                    BRequirement::create([
                        'b_criteria_id' => $criteria->id,
                        'requirement_description' => $requirement['requirement_description'] ?? null,
                        'point_value' => $requirement['point_value'] ?? null,
                    ]);
                }
            }
        }

        return [
            'status' => 201,
            'message' => 'Criteria created successfully.',
            'data' => $criteria->load('bRequirements')
        ];
    }

    //CRITERIA C
    public function getAllCCriterias()
    {
        $criteria = CCriteria::with('cRequirements')->get();

        return [
            'status' => 200,
            'message' => 'Criteria retrieved successfully.',
            'data' => $criteria
        ];
    } 

    public function createC(array $data)
    {
        $requirements = $data['cRequirements'] ?? [];
        unset($data['cRequirements']);

        $criteria = CCriteria::create($data);

        // Loop through the extracted requirements
        if (!empty($requirements) && is_array($requirements)) {
            foreach ($requirements as $requirement) {
                if (!empty($requirement['requirement_description']) || isset($requirement['point_value'])) {
                    CRequirement::create([
                        'c_criteria_id' => $criteria->id,
                        'requirement_description' => $requirement['requirement_description'] ?? null,
                        'point_value' => $requirement['point_value'] ?? null,
                    ]);
                }
            }
        }

        return [
            'status' => 201,
            'message' => 'Criteria created successfully.',
            'data' => $criteria->load('cRequirements')
        ];
    }

    //CRITERIA D
    public function getAllDCriterias()
    {
        $criteria = DCriteria::with('dRequirements')->get();

        return [
            'status' => 200,
            'message' => 'Criteria retrieved successfully.',
            'data' => $criteria
        ];
    }

    public function createD(array $data)
    {
        $requirements = $data['dRequirements'] ?? [];
        unset($data['dRequirements']);

        $criteria = DCriteria::create($data);

        // Loop through the extracted requirements
        if (!empty($requirements) && is_array($requirements)) {
            foreach ($requirements as $requirement) {
                if (!empty($requirement['requirement_description']) || isset($requirement['point_value'])) {
                    DRequirement::create([
                        'd_criteria_id' => $criteria->id,
                        'requirement_description' => $requirement['requirement_description'] ?? null,
                        'point_value' => $requirement['point_value'] ?? null,
                    ]);
                }
            }
        }

        return [
            'status' => 201,
            'message' => 'Criteria created successfully.',
            'data' => $criteria->load('dRequirements')
        ];
    }

    //Criteria E
    public function getAllECriterias()
    {
        $criteria = ECriteria::with('eRequirements')->get();

        return [
            'status' => 200,
            'message' => 'Criteria retrieved successfully.',
            'data' => $criteria
        ];
    }

    public function createE(array $data)
    {
        $requirements = $data['eRequirements'] ?? [];
        unset($data['eRequirements']);

        $criteria = ECriteria::create($data);

        // Loop through the extracted requirements
        if (!empty($requirements) && is_array($requirements)) {
            foreach ($requirements as $requirement) {
                if (!empty($requirement['requirement_description']) || isset($requirement['point_value'])) {
                    ERequirement::create([
                        'e_criteria_id' => $criteria->id,
                        'requirement_description' => $requirement['requirement_description'] ?? null,
                        'point_value' => $requirement['point_value'] ?? null,
                    ]);
                }
            }
        }

        return [
            'status' => 201,
            'message' => 'Criteria created successfully.',
            'data' => $criteria->load('eRequirements')
        ];
    }
}