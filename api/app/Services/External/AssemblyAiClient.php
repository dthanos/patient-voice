<?php

namespace App\Services\External;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AssemblyAiClient
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => env('ASSEMBLYAI_API_URL'),
            'headers' => [
                'authorization' => env('ASSEMBLYAI_API_KEY'),
                'content-type' => 'application/json',
            ]
        ]);
    }

    public function transcribe(Media $media): string
    {
        try {
            // Step 1: Upload local file
            $uploadResponse = $this->client->post('upload', [
                'body' => fopen($media->getPath(), 'r'),
            ]);

            $uploadData = json_decode($uploadResponse->getBody(), true);
            $uploadUrl = $uploadData['upload_url'];

            // Step 2: Submit for transcription using upload URL
            $transcribeResponse = $this->client->post('transcript', [
                'json' => [
                    'audio_url' => $uploadUrl,
                ]
            ]);

            $transcription = json_decode($transcribeResponse->getBody(), true);
            $transcriptId = $transcription['id'];

            // Step 3: Poll until complete
            while (true) {
                sleep(3);

                $pollResponse = $this->client->get("transcript/{$transcriptId}");
                $transcription = json_decode($pollResponse->getBody(), true);

                if ($transcription['status'] === 'completed') {
                    $transcript = $transcription['text'];
                    break;
                }

                if ($transcription['status'] === 'error')
                    throw new Exception("Transcription failed: " . $transcription['error']);
            }
        } catch (GuzzleException|Exception $e) {
            $transcript = "Error: " . $e->getMessage();
        }

        return $transcript;
    }

}
