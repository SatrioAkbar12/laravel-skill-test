<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogDownloadTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_success_download_log_file()
    {
        $path = storage_path('logs/laravel.log');

        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }
        file_put_contents($path, 'Test log content');

        $response = $this->actingAs($this->user)->get(route('logs.download'));

        $response->assertOK();
        $response->assertHeader('Content-Disposition', 'attachment; filename=laravel.log');
        $response->assertDownload('laravel.log');
    }

    public function test_log_file_not_found()
    {
        $path = storage_path('logs/laravel.log');

        if (file_exists($path)) {
            unlink($path);
        }

        $response = $this->actingAs($this->user)->get(route('logs.download'));

        $response->assertStatus(404);
    }
}
