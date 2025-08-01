<?php

namespace App\Http\Controllers;

use App\Services\External\AssemblyAiClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    private string $uploadDir = 'uploads';

    public function index(Request $request)
    {
        $method = $request->method();

        return match ($method) {
            'OPTIONS' => response('', 204)->withHeaders([
                'Access-Control-Allow-Methods' => 'POST, GET, OPTIONS, PATCH, HEAD',
            ]),
            'HEAD' => $this->getOffset($request),
            'POST' => $this->initUpload($request),
            'PATCH' => $this->writeChunk($request),
            'DELETE' => $this->removeFile($request),
            default => response('Method not allowed', 405),
        };
    }

    /** Initiating file storing */
    private function initUpload(Request $request)
    {
        $uploadLength = $request->header('Upload-Length');
        $metadataHeader = $request->header('Upload-Metadata', '');

        $uuid = Str::uuid();
        $filePath = "$this->uploadDir/$uuid.part";
        $metaPath = "$this->uploadDir/$uuid.info";

        Storage::put($filePath, ''); // Starting the file
        Storage::put($metaPath, json_encode([ // Storing metadata
            'offset' => 0,
            'length' => (int)$uploadLength,
            'metadata' => $this->parseMetadata($metadataHeader),
        ]));

        return response('', 201)->withHeaders([
            'Location' => "/api/upload?upload_id=$uuid"
        ]);
    }

    private function getOffset(Request $request)
    {
        $uploadId = $request->query('upload_id');
        $metaPath = "$this->uploadDir/$uploadId.info";

        if (!Storage::exists($metaPath))
            return response('Not Found', 404);

        $info = json_decode(Storage::get($metaPath), true);

        return response('', 200)->withHeaders([
            'Upload-Offset' => $info['offset'],
            'Upload-Length' => $info['length'],
        ]);
    }

    private function writeChunk(Request $request)
    {
        $uploadId = $request->query('upload_id');
        $offset = (int) $request->header('Upload-Offset');

        $media = null;
        $metaPath = "$this->uploadDir/$uploadId.info";
        $filePath = "$this->uploadDir/$uploadId.part";

        if (!Storage::exists($metaPath) || !Storage::exists($filePath))
            return response('Not Found', 404);

        $info = json_decode(Storage::get($metaPath), true);

        if ($info['offset'] !== $offset)
            return response('Offset mismatch', 409);

        $data = file_get_contents('php://input');
        $bytesWritten = strlen($data);

        // Appending to existing file
        Storage::append($filePath, $data);

        // Updating offset
        $info['offset'] += $bytesWritten;
        Storage::put($metaPath, json_encode($info));

        // Checking if upload is complete
        if ($info['offset'] >= $info['length']) {
            $finalPath = "$this->uploadDir/$uploadId";
            Storage::move($filePath, $finalPath);

            $media = auth()->user()// Storing file into user's uploads
                ->addMedia(Storage::path($finalPath))// absolute path
                ->usingFileName($info['metadata']['filename'])
                ->toMediaCollection('uploads');
            // Deleting temporary files
            Storage::delete($metaPath);
            Storage::delete($filePath);
        }

        return response('', 201)->withHeaders([
            'Upload-Offset' => $info['offset'],
            'Location' => "/api/upload?upload_id=$uploadId",
            'x-media-id' => $media ? $media->id : null,
        ]);
    }

    private function removeFile(Request $request)
    {
        $uploadId = $request->query('upload_id');

        if (Storage::exists($metaPath = "$this->uploadDir/$uploadId.info"))
            Storage::delete($metaPath);
        if (Storage::exists($filePath = "$this->uploadDir/$uploadId.part"))
            Storage::delete($filePath);

        return response('', 204);
    }

    private function parseMetadata(string $header): array
    {
        $metadata = [];

        foreach (explode(',', $header) as $pair) {
            [$key, $value] = array_pad(explode(' ', trim($pair)), 2, null);
            $metadata[$key] = base64_decode($value);
        }

        return $metadata;
    }
}
