<?php

namespace App\Http\Controllers;

use App\Http\Requests\Patient\PatientCreateRequest;
use App\Http\Requests\Patient\PatientUpdateRequest;
use App\Http\Requests\SearchRequest;
use App\Models\Patient;
use App\Resources\PatientResource;
use App\Services\PatientService;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function __construct(private readonly PatientService $patientService){}

    /**
     * Display a listing of the resource.
     */
    public function index(SearchRequest $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $requestData = $request->validated();

        $response = $this->patientService->index($requestData);

        return PatientResource::collection($response['data'])->additional(['total' => $response['total']]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PatientCreateRequest $request): PatientResource
    {
        return PatientResource::make($this->patientService->create($request->validated()));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $patient): PatientResource
    {
        return PatientResource::make($this->patientService->show($patient));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PatientUpdateRequest $request, string $patient): PatientResource
    {
        return PatientResource::make($this->patientService->update($patient, $request->validated()));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $patient): PatientResource
    {
        return PatientResource::make($this->patientService->delete($patient));
    }
}
