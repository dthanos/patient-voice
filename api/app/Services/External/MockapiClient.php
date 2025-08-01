<?php

namespace App\Services\External;

use App\Models\Patient;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MockapiClient
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => env('MOCKAPI_API_URL'),
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);
    }

    public function getPatients(array $query = [])
    {
        try {
            $response = json_decode($this->client->get('/patient', ['query' => $query])->getBody(), true);

            // Remove pagination to get the total of records
            unset($query['page'], $query['limit']);
            $total = count(json_decode($this->client->get('/patient', ['query' => $query])->getBody(), true));
        }catch (GuzzleException $e){
            $response = [];
            $total = 0;
        }

        return [
            'data' => $response,
            'total' => $total,
        ];
    }

    public function getPatient($id)
    {
        try {
            $response = $this->client->get("/patient/$id")->getBody();
        }catch (GuzzleException $e){
            $response = "{}";
        }

        return json_decode($response, true);
    }

    public function createPatient(array $body = [])
    {
        try {
            $response = $this->client->post('/patient', ['json' => $body])->getBody();
        }catch (GuzzleException $e){
            $response = "{}";
        }

        return json_decode($response, true);
    }

    public function updatePatient(string $id, array $body = [])
    {
        try {
            $response = $this->client->put("/patient/$id", ['json' => $body])->getBody();
        }catch (GuzzleException $e){
            $response = "{}";
        }

        return json_decode($response, true);
    }

    public function deletePatient(string $id)
    {
        try {
            $response = $this->client->delete("/patient/$id")->getBody();
        }catch (GuzzleException $e){
            $response = "{}";
        }

        return json_decode($response, true);
    }
}
