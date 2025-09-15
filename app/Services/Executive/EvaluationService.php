<?php

namespace App\Services\Executive;

use App\Models\ACriteria;
use App\Models\BCriteria;
use App\Models\CCriteria;
use App\Models\DCriteria;
use App\Models\ECriteria;

class EvaluationService
{
 
 protected $officeMap = [
        'Administrative Service' => 'as',
        'Legal Division' => 'legal',
        'Certification Office' => 'co',
        'Financial and Management Service' => 'fms',
        'National Institute for Technical Education and Skills Development' => 'nitesd',
        'Public Information and Assistance Division' => 'piad',
        'Planning Office' => 'planning',
        'Partnership and Linkages Office' => 'plo',
        'Regional Operations Management Office' => 'romo',
        'Information and Communication Office' => 'icto',
        'World Skills' => 'ws',
    ];

    protected $criteriaModels = [
        'a' => ACriteria::class,
        'b' => BCriteria::class,
        'c' => CCriteria::class,
        'd' => DCriteria::class,
        'e' => ECriteria::class,
    ];

    /**
     * Get criteria + requirements for a user's office
     */
    public function getCriteriaForOffice(string $office)
    {
        $officeKey = $this->officeMap[$office] ?? null;

        if (!$officeKey) return [];

        $result = [];

        $relationshipMap = [
            'a' => 'aRequirements',
            'b' => 'bRequirements',
            'c' => 'cRequirements',
            'd' => 'dRequirements',
            'e' => 'eRequirements',
        ];

        foreach ($this->criteriaModels as $key => $modelClass) {
            $relationship = $relationshipMap[$key]; // get the correct relationship string

            $criterias = $modelClass::with($relationship)
                ->where($officeKey, true)
                ->get();

            $result[$key] = $criterias;
        }

        return $result;
    }


      private function fetchCriteria($modelClass, $relationship, string $office)
    {
        $officeKey = $this->officeMap[$office] ?? null;
        if (!$officeKey) {
            return [
                'data' => [],
                'message' => 'Invalid office',
            ];
        }

        $criterias = $modelClass::with($relationship)
            ->where($officeKey, true)
            ->get();

        if ($criterias->isEmpty()) {
            return [
                'data' => [],
                'message' => 'No criteria found for this office',
            ];
        }

        return [
            'data' => $criterias,
            'message' => 'Criteria retrieved successfully',
        ];
    }

    // ------------------- Public functions -------------------

    public function getACriteriaForOffice(string $office)
    {
        return $this->fetchCriteria(ACriteria::class, 'aRequirements', $office);
    }

    public function getBCriteriaForOffice(string $office)
    {
        return $this->fetchCriteria(BCriteria::class, 'bRequirements', $office);
    }

    public function getCCriteriaForOffice(string $office)
    {
        return $this->fetchCriteria(CCriteria::class, 'cRequirements', $office);
    }

    public function getDCriteriaForOffice(string $office)
    {
        return $this->fetchCriteria(DCriteria::class, 'dRequirements', $office);
    }

    public function getECriteriaForOffice(string $office)
    {
        return $this->fetchCriteria(ECriteria::class, 'eRequirements', $office);
    }
    
}