<?php

namespace App\Services\External;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class OpenAiClient
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => env('OPENAI_API_URL'),
            'headers' => [
                'Authorization' => "Bearer " . env('OPENAI_API_KEY'),
            ]
        ]);
    }

    public function summarize(string $text): string
    {
        try {
            $response = $this->client->post('chat/completions', [
                'json' => [
                    'model' => 'gpt-3.5-turbo-1106',
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a helpful assistant that summarizes texts.'],
                        ['role' => 'user', 'content' => "Make a summary of the following:\n\n$text"],
                    ],
                    'temperature' => 0.7,
                ],
            ]);

            $result = json_decode($response->getBody(), true);
            $summary = $result['choices'][0]['message']['content'];
        } catch (GuzzleException|Exception $e) {
            $summary = "Error: " . $e->getMessage();
        }

        return $summary;
    }

}
