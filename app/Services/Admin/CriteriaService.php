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
use Illuminate\Http\Request;

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
  
    public function update(array $data, int $id)
    {
        $criteria = ACriteria::findOrFail($id);
        $criteria->update($data);

        $requirements = $data['aRequirements'] ?? [];
        unset($data['aRequirements']);

        $incomingIds = collect($requirements)->pluck('id')->filter()->toArray();
        ARequirement::where('a_criteria_id', $criteria->id)
            ->whereNotIn('id', $incomingIds)
            ->delete();

        foreach ($requirements as $requirement) {
            if (!empty($requirement['requirement_description']) || isset($requirement['point_value'])) {
                if (!empty($requirement['id'])) {
                    ARequirement::where('id', $requirement['id'])
                        ->where('a_criteria_id', $criteria->id)
                        ->update([
                            'requirement_description' => $requirement['requirement_description'],
                            'point_value' => $requirement['point_value'],
                        ]);
                } else {
                    ARequirement::create([
                        'a_criteria_id' => $criteria->id,
                        'requirement_description' => $requirement['requirement_description'],
                        'point_value' => $requirement['point_value'],
                    ]);
                }
            }
        }

        return [
            'status' => 200,
            'message' => 'Criteria updated successfully.',
            'data' => $criteria->load('aRequirements'),
        ];
    }
    
    public function getCriteriaId(int $id)
    {
        $criteria = ACriteria::with('aRequirements')->findOrFail($id);

        return [
            'status' => 200,
            'message' => 'Criteria retrieved successfully.',
            'data' => $criteria
        ];
    }

    public function BoolCriteriaA(int $id, array $fields = [])
    {
        $criteria = ACriteria::findOrFail($id);

        $booleanFields = [
            'bro_small', 'bro_medium', 'bro_large',
            'gp_small', 'gp_medium', 'gp_large',
            'bti_rtcstc', 'bti_ptcdtc', 'bti_tas'
        ];

        // Loop through all boolean fields
        foreach ($booleanFields as $field) {
            // If this field is included in $fields, set to true, otherwise false
            $criteria->$field = in_array($field, $fields);
        }

        $criteria->save();
        return [
            'status' => 200,
            'message' => 'Criteria updated successfully.',
            'data' => $criteria
        ];
    }

        public function executiveConfigA(int $id, array $fields = [])
    {
        $criteria = ACriteria::findOrFail($id);

        $booleanFields = [
        'as',
        'legal',
        'co',
        'fms',
        'nitesd',
        'piad',
        'planning',
        'plo',
        'romo',
        'icto',
        'ws',
        'gadtwc',
        'cbtveto'
        ];

        // Loop through all boolean fields
        foreach ($booleanFields as $field) {
            // If this field is included in $fields, set to true, otherwise false
            $criteria->$field = in_array($field, $fields);
        }

        $criteria->save();
        return [
            'status' => 200,
            'message' => 'Criteria updated successfully.',
            'data' => $criteria
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
        $criteria = ACriteria::findOrFail($id);
        $criteria->delete();

        return [
            'status' => 200,
            'message' => 'Criteria and its requirements deleted successfully.'
        ];
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

    public function updateB(array $data, int $id)
    {
        $criteria = BCriteria::findOrFail($id);
        $criteria->update($data);

        $requirements = $data['bRequirements'] ?? [];
        unset($data['bRequirements']);

        $incomingIds = collect($requirements)->pluck('id')->filter()->toArray();
        BRequirement::where('b_criteria_id', $criteria->id)
            ->whereNotIn('id', $incomingIds)
            ->delete();

        foreach ($requirements as $requirement) {
            if (!empty($requirement['requirement_description']) || isset($requirement['point_value'])) {
                if (!empty($requirement['id'])) {
                    BRequirement::where('id', $requirement['id'])
                        ->where('b_criteria_id', $criteria->id)
                        ->update([
                            'requirement_description' => $requirement['requirement_description'],
                            'point_value' => $requirement['point_value'],
                        ]);
                } else {
                    BRequirement::create([
                        'b_criteria_id' => $criteria->id,
                        'requirement_description' => $requirement['requirement_description'],
                        'point_value' => $requirement['point_value'],
                    ]);
                }
            }
        }

        return [
            'status' => 200,
            'message' => 'Criteria updated successfully.',
            'data' => $criteria->load('bRequirements'),
        ];
    }

    public function getBCriteriaId(int $id)
    {
        $criteria = BCriteria::with('bRequirements')->findOrFail($id);

        return [
            'status' => 200,
            'message' => 'Criteria retrieved successfully.',
            'data' => $criteria
        ];
    }

    public function BoolCriteriaB(int $id, array $fields = [])
    {
        $criteria = BCriteria::findOrFail($id);

        $booleanFields = [
            'bro_small', 'bro_medium', 'bro_large',
            'gp_small', 'gp_medium', 'gp_large',
            'bti_rtcstc', 'bti_ptcdtc', 'bti_tas'
        ];

        // Loop through all boolean fields
        foreach ($booleanFields as $field) {
            // If this field is included in $fields, set to true, otherwise false
            $criteria->$field = in_array($field, $fields);
        }

        $criteria->save();
        return [
            'status' => 200,
            'message' => 'Criteria updated successfully.',
            'data' => $criteria
        ];
    }

    public function executiveConfigB(int $id, array $fields = [])
    {
        $criteria = BCriteria::findOrFail($id);

        $booleanFields = [
        'as',
        'legal',
        'co',
        'fms',
        'nitesd',
        'piad',
        'planning',
        'plo',
        'romo',
        'icto',
        'ws',
        'gadtwc',
        'cbtveto'
        ];

        // Loop through all boolean fields
        foreach ($booleanFields as $field) {
            // If this field is included in $fields, set to true, otherwise false
            $criteria->$field = in_array($field, $fields);
        }

        $criteria->save();
        return [
            'status' => 200,
            'message' => 'Criteria updated successfully.',
            'data' => $criteria
        ];
    }

    public function deleteCriteriaB(int $id)
    {
        $criteria = BCriteria::findOrFail($id);
        $criteria->delete();

        return [
            'status' => 200,
            'message' => 'Criteria and its requirements deleted successfully.'
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

    public function updateC(array $data, int $id)
    {
        $criteria = CCriteria::findOrFail($id);
        $criteria->update($data);

        $requirements = $data['cRequirements'] ?? [];
        unset($data['cRequirements']);

        $incomingIds = collect($requirements)->pluck('id')->filter()->toArray();
        CRequirement::where('c_criteria_id', $criteria->id)
            ->whereNotIn('id', $incomingIds)
            ->delete();

        foreach ($requirements as $requirement) {
            if (!empty($requirement['requirement_description']) || isset($requirement['point_value'])) {
                if (!empty($requirement['id'])) {
                    CRequirement::where('id', $requirement['id'])
                        ->where('c_criteria_id', $criteria->id)
                        ->update([
                            'requirement_description' => $requirement['requirement_description'],
                            'point_value' => $requirement['point_value'],
                        ]);
                } else {
                    CRequirement::create([
                        'c_criteria_id' => $criteria->id,
                        'requirement_description' => $requirement['requirement_description'],
                        'point_value' => $requirement['point_value'],
                    ]);
                }
            }
        }

        return [
            'status' => 200,
            'message' => 'Criteria updated successfully.',
            'data' => $criteria->load('cRequirements'),
        ];
    }

    public function getCCriteriaId(int $id)
    {
        $criteria = CCriteria::with('cRequirements')->findOrFail($id);

        return [
            'status' => 200,
            'message' => 'Criteria retrieved successfully.',
            'data' => $criteria
        ];
    }

    public function BoolCriteriaC(int $id, array $fields = [])
    {
        $criteria = CCriteria::findOrFail($id);

        $booleanFields = [
            'bro_small', 'bro_medium', 'bro_large',
            'gp_small', 'gp_medium', 'gp_large',
            'bti_rtcstc', 'bti_ptcdtc', 'bti_tas'
        ];

        // Loop through all boolean fields
        foreach ($booleanFields as $field) {
            // If this field is included in $fields, set to true, otherwise false
            $criteria->$field = in_array($field, $fields);
        }

        $criteria->save();
        return [
            'status' => 200,
            'message' => 'Criteria updated successfully.',
            'data' => $criteria
        ];
    }   

    public function executiveConfigC(int $id, array $fields = [])
    {
        $criteria = CCriteria::findOrFail($id);

        $booleanFields = [
        'as',
        'legal',
        'co',
        'fms',
        'nitesd',
        'piad',
        'planning',
        'plo',
        'romo',
        'icto',
        'ws',
        'gadtwc',
        'cbtveto'
        ];

        // Loop through all boolean fields
        foreach ($booleanFields as $field) {
            // If this field is included in $fields, set to true, otherwise false
            $criteria->$field = in_array($field, $fields);
        }

        $criteria->save();
        return [
            'status' => 200,
            'message' => 'Criteria updated successfully.',
            'data' => $criteria
        ];
    }

    public function deleteCriteriaC(int $id)
    {
        $criteria = CCriteria::findOrFail($id);
        $criteria->delete();

        return [
            'status' => 200,
            'message' => 'Criteria and its requirements deleted successfully.'
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

    public function updateD(array $data, int $id)
    {
        $criteria = DCriteria::findOrFail($id);
        $criteria->update($data);

        $requirements = $data['dRequirements'] ?? [];
        unset($data['dRequirements']);

        $incomingIds = collect($requirements)->pluck('id')->filter()->toArray();
        DRequirement::where('d_criteria_id', $criteria->id)
            ->whereNotIn('id', $incomingIds)
            ->delete();

        foreach ($requirements as $requirement) {
            if (!empty($requirement['requirement_description']) || isset($requirement['point_value'])) {
                if (!empty($requirement['id'])) {
                    DRequirement::where('id', $requirement['id'])
                        ->where('d_criteria_id', $criteria->id)
                        ->update([
                            'requirement_description' => $requirement['requirement_description'],
                            'point_value' => $requirement['point_value'],
                        ]);
                } else {
                    DRequirement::create([
                        'd_criteria_id' => $criteria->id,
                        'requirement_description' => $requirement['requirement_description'],
                        'point_value' => $requirement['point_value'],
                    ]);
                }
            }
        }

        return [
            'status' => 200,
            'message' => 'Criteria updated successfully.',
            'data' => $criteria->load('dRequirements'),
        ];
    }

    public function getDCriteriaId(int $id)
    {
        $criteria = DCriteria::with('dRequirements')->findOrFail($id);

        return [
            'status' => 200,
            'message' => 'Criteria retrieved successfully.',
            'data' => $criteria
        ];
    }

    public function BoolCriteriaD(int $id, array $fields = [])
    {
        $criteria = DCriteria::findOrFail($id);

        $booleanFields = [
            'bro_small', 'bro_medium', 'bro_large',
            'gp_small', 'gp_medium', 'gp_large',
            'bti_rtcstc', 'bti_ptcdtc', 'bti_tas'
        ];

        // Loop through all boolean fields
        foreach ($booleanFields as $field) {
            // If this field is included in $fields, set to true, otherwise false
            $criteria->$field = in_array($field, $fields);
        }

        $criteria->save();
        return [
            'status' => 200,
            'message' => 'Criteria updated successfully.',
            'data' => $criteria
        ];
    }

    public function executiveConfigD(int $id, array $fields = [])
    {
        $criteria = DCriteria::findOrFail($id);

        $booleanFields = [
        'as',
        'legal',
        'co',
        'fms',
        'nitesd',
        'piad',
        'planning',
        'plo',
        'romo',
        'icto',
        'ws',
        'gadtwc',
        'cbtveto'
        ];

        // Loop through all boolean fields
        foreach ($booleanFields as $field) {
            // If this field is included in $fields, set to true, otherwise false
            $criteria->$field = in_array($field, $fields);
        }

        $criteria->save();
        return [
            'status' => 200,
            'message' => 'Criteria updated successfully.',
            'data' => $criteria
        ];
    }

    public function deleteCriteriaD(int $id)
    {
        $criteria = DCriteria::findOrFail($id);
        $criteria->delete();

        return [
            'status' => 200,
            'message' => 'Criteria and its requirements deleted successfully.'
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
    public function updateE(array $data, int $id)
    {
        $criteria = ECriteria::findOrFail($id);
        $criteria->update($data);

        $requirements = $data['eRequirements'] ?? [];
        unset($data['eRequirements']);

        $incomingIds = collect($requirements)->pluck('id')->filter()->toArray();
        ERequirement::where('e_criteria_id', $criteria->id)
            ->whereNotIn('id', $incomingIds)
            ->delete();

        foreach ($requirements as $requirement) {
            if (!empty($requirement['requirement_description']) || isset($requirement['point_value'])) {
                if (!empty($requirement['id'])) {
                    ERequirement::where('id', $requirement['id'])
                        ->where('e_criteria_id', $criteria->id)
                        ->update([
                            'requirement_description' => $requirement['requirement_description'],
                            'point_value' => $requirement['point_value'],
                        ]);
                } else {
                    ERequirement::create([
                        'e_criteria_id' => $criteria->id,
                        'requirement_description' => $requirement['requirement_description'],
                        'point_value' => $requirement['point_value'],
                    ]);
                }
            }
        }

        return [
            'status' => 200,
            'message' => 'Criteria updated successfully.',
            'data' => $criteria->load('eRequirements'),
        ];
    }

    public function getECriteriaId(int $id)
    {
        $criteria = ECriteria::with('eRequirements')->findOrFail($id);

        return [
            'status' => 200,
            'message' => 'Criteria retrieved successfully.',
            'data' => $criteria
        ];
    }

    public function BoolCriteriaE(int $id, array $fields = [])
    {
        $criteria = ECriteria::findOrFail($id);

        $booleanFields = [
            'bro_small', 'bro_medium', 'bro_large',
            'gp_small', 'gp_medium', 'gp_large',
            'bti_rtcstc', 'bti_ptcdtc', 'bti_tas'
        ];

        // Loop through all boolean fields
        foreach ($booleanFields as $field) {
            // If this field is included in $fields, set to true, otherwise false
            $criteria->$field = in_array($field, $fields);
        }

        $criteria->save();
        return [
            'status' => 200,
            'message' => 'Criteria updated successfully.',
            'data' => $criteria
        ];
    }

    public function executiveConfigE(int $id, array $fields = [])
    {
        $criteria = ECriteria::findOrFail($id);

        $booleanFields = [
        'as',
        'legal',
        'co',
        'fms',
        'nitesd',
        'piad',
        'planning',
        'plo',
        'romo',
        'icto',
        'ws',
        'gadtwc',
        'cbtveto'
        ];

        // Loop through all boolean fields
        foreach ($booleanFields as $field) {
            // If this field is included in $fields, set to true, otherwise false
            $criteria->$field = in_array($field, $fields);
        }

        $criteria->save();
        return [
            'status' => 200,
            'message' => 'Criteria updated successfully.',
            'data' => $criteria
        ];
    }

    public function deleteCriteriaE(int $id)
    {
        $criteria = ECriteria::findOrFail($id);
        $criteria->delete();

        return [
            'status' => 200,
            'message' => 'Criteria and its requirements deleted successfully.'
        ];
    }
}