<?php

namespace App\Services\Admin;

use App\Models\ACriteria;

class CriteriaService
{
    /**
     * Get all criteria.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllCriterias()
    {
        return ACriteria::all();
    }

    /**
     * Create a new criteria.
     *
     * @param array $data
     * @return \App\Models\ACriteria
     */
    public function create(array $data)
    {
        $criteria = ACriteria::create($data);

        return [
            'status' => 201,
            'message' => 'Criteria created successfully.',
            'data' => $criteria
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