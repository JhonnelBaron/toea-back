<?php

namespace App\Services\Admin;

use App\Models\ACriteria;
use App\Models\Requirements\ARequirement;

class CriteriaService
{
    /**
     * Get all criteria.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
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

    public function addRequirement(array $data)
    {
        $requirement = ARequirement::create($data);

        return [
            'status' => 201,
            'message' => 'Requirement created successfully.',
            'data' => $requirement
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
}