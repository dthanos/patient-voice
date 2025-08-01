<?php

namespace App\Http\Controllers;

use App\Services\External\AssemblyAiClient;
use App\Services\External\OpenAiClient;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class TranscriptionController extends Controller
{

    public function transcribe(Request $request): \Illuminate\Http\JsonResponse
    {
        $mediaId = $request->input("mediaId");
        $assemblyAiClient = new AssemblyAiClient();
        $transcript = $assemblyAiClient->transcribe(Media::query()->find($mediaId));

        $error = str_starts_with($transcript, 'Error:');
        return response()->json(
            $error ? ['error' => $transcript] : ["transcript" => $transcript],
            $error ? 500 : 200
        );
    }

    public function summarize(Request $request): \Illuminate\Http\JsonResponse
    {
        $text = $request->input("text");
        $openAiClient = new OpenAiClient();
        $summary = $openAiClient->summarize($text);

        $error = str_starts_with($summary, 'Error:');
        return response()->json(
            $error ? ['error' => $summary] : ["summary" => $summary],
            $error ? 500 : 200
        );
    }
}
