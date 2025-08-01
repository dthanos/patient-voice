<?php

namespace App\Repositories;

use App\Models\Patient;

class PatientRepository
{
    public function create(array $attributes): Patient
    {
        return Patient::create(array_merge(['ip' => request()->ip()],$attributes));
    }

    public function update(Patient $patient, array $attributes): Patient
    {
        $patient->update($attributes);
        return $patient->refresh();
    }

}
