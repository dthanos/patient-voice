<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Nette\Utils\Random;
use Tests\TestCase;

class UploadTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        $this->user = User::factory()->create();
    }

    public function test_post_upload_creates_files_and_returns_location()
    {
        Sanctum::actingAs($this->user);
        $response = $this->withHeaders([
            'Upload-Length' => '100',
            'Upload-Metadata' => 'filename ' . base64_encode('test.txt'),
        ])->post('/api/upload');

        $response->assertCreated();
        $this->assertStringContainsString('/api/upload?upload_id=', $response->headers->get('Location'));
    }

    public function test_head_get_offset_returns_correct_headers()
    {
        Sanctum::actingAs($this->user);
        $uploadId = Str::uuid();

        Storage::put("uploads/$uploadId.info", json_encode([
            'offset' => 10,
            'length' => 100,
            'metadata' => ['filename' => 'test.txt']
        ]));

        $response = $this->head("/api/upload?upload_id=$uploadId");

        $response->assertOk();
        $response->assertHeader('Upload-Offset', 10);
        $response->assertHeader('Upload-Length', 100);
    }

    public function test_patch_write_chunk_appends_data_and_completes_upload()
    {
        Sanctum::actingAs($this->user);

        $uploadId = Str::uuid();
        $metadata = ['filename' => 'test.txt'];

        Storage::put("uploads/$uploadId.part", '');
        Storage::put("uploads/$uploadId.info", json_encode([
            'offset' => 0,
            'length' => 10,
            'metadata' => $metadata,
        ]));

        $response = $this->withHeaders([
            'Upload-Offset' => '0'
        ])->call(
            'PATCH',
            "/api/upload?upload_id=$uploadId",
            [], [], [], [],
            Random::generate()
        );

        $response->assertCreated();
        $response->assertHeader('Upload-Offset', 0);

        Storage::assertMissing("uploads/$uploadId");
    }

    public function test_delete_file_removes_temp_files()
    {
        Sanctum::actingAs($this->user);
        $uploadId = Str::uuid();

        Storage::put("uploads/$uploadId.part", '12345');
        Storage::put("uploads/$uploadId.info", json_encode(['offset' => 5, 'length' => 10]));

        $response = $this->delete("/api/upload?upload_id=$uploadId");

        $response->assertNoContent();
        Storage::assertMissing("uploads/$uploadId.part");
        Storage::assertMissing("uploads/$uploadId.info");
    }


    public function test_offset_mismatch_returns_conflict()
    {
        Sanctum::actingAs($this->user);
        $uploadId = Str::uuid();

        Storage::put("uploads/$uploadId.part", '');
        Storage::put("uploads/$uploadId.info", json_encode([
            'offset' => 5,
            'length' => 20,
            'metadata' => ['filename' => 'test.txt'],
        ]));

        $response = $this->withHeaders([
            'Upload-Offset' => '0'
        ])->call(
            'PATCH',
            "/api/upload?upload_id=$uploadId",
            [], [], [], [],
            Random::generate()
        );

        $response->assertStatus(409);
    }
}
