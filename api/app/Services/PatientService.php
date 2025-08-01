<?php

namespace App\Services;

use App\Models\Patient;
use App\Services\External\MockapiClient;

readonly class PatientService
{
    public function __construct(protected MockapiClient $mockapiClient) {}

    public function index(array $params): array
    {
        $query = [
            'page' => $params['page'],
            'limit' => '999999999',
        ];
        if(array_key_exists('filters', $params) && !empty($params['filters']))
            foreach ($params['filters'] as $field=>$value) {
                if($field !== 'age') $query[$field] = $value;
            }
//        if(array_key_exists('search', $params) && !empty($params['search']))
//            $query['name'] = $params['search'];
        if(array_key_exists('sort', $params) && !empty($params['sort'])) {
            $query['sortBy'] = $params['sort'];
            if(array_key_exists('sortDesc', $params))
                $query['order'] = $params['sortDesc']  === 'true' ? 'desc' : 'asc';
        }

        $response = $this->mockapiClient->getPatients($query);

        // Manual filtering by age range
        if(
            array_key_exists('filters', $params) && !empty($params['filters']) &&
            array_key_exists('age', $params['filters']) && !empty($params['filters']['age'])
        ) {
            $ageArray = explode('-', $params['filters']['age']);
            $patients = array_filter($response['data'], fn($i) => in_array($i['age'], range($ageArray[0], $ageArray[1])));
            $response = [
                'data' => $patients,
                'total' => count($patients)
            ];
        }

        return [
            'data' => collect($response['data'])->map(fn ($i) => new Patient($i)),
            'total' => $response['total'],
        ];
    }
    public function show(string $id): Patient
    {
        return new Patient($this->mockapiClient->getPatient($id));
    }
    public function create(array $attributes): Patient
    {
        return new Patient($this->mockapiClient->createPatient($attributes));
    }
    public function update(string $id, array $attributes): Patient
    {
        return new Patient($this->mockapiClient->updatePatient($id, $attributes));
    }
    public function delete(string $id): Patient
    {
        return new Patient($this->mockapiClient->deletePatient($id));
    }
}
