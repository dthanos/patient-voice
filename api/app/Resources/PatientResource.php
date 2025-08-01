<?php

namespace App\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return
            [
                'id' => $this->id,
                'name' => $this->name,
                'email' => $this->email,
                'gender' => $this->gender,
                'disease' => $this->disease,
                'medication' => $this->medication,
                'age' => $this->age,
            ];
    }
}
