<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class MaintenanceModeTest extends TestCase
{
    protected function tearDown(): void
    {
        Artisan::call('up');

        parent::tearDown();
    }

    public function test_maintenance_mode_renders_custom_503_page_with_under_maintenance_banner(): void
    {
        Artisan::call('down');

        $response = $this->get('/');

        $response->assertStatus(503);
        $response->assertSee('System Notice');
        $response->assertSee('The platform is currently under maintenance. We will be back online shortly.');
        $response->assertSee("We'll Be Right Back!", false);
        $response->assertSee('Check Again');
        $response->assertSee('maintenance-banner');
    }

    public function test_503_view_renders_cleanly_with_custom_message(): void
    {
        $exception = new HttpException(503, 'System upgrades in progress. Voting will resume at 8:00 PM.');

        $view = $this->view('errors.503', [
            'exception' => $exception,
        ]);

        $view->assertSee('System Notice');
        $view->assertSee('System upgrades in progress. Voting will resume at 8:00 PM.');
        $view->assertSee('Check Again');
    }

    public function test_503_view_supports_prerender_retry_after(): void
    {
        $view = $this->view('errors.503', [
            'retryAfter' => 60,
        ]);

        $view->assertSee('Auto-checking in <strong id="countdownSecs">60</strong>s', false);
        $view->assertSee('<meta http-equiv="refresh" content="60">', false);
    }

    public function test_artisan_down_with_render_errors_503_works_and_outputs_banner(): void
    {
        Artisan::call('down', [
            '--render' => 'errors::503',
        ]);

        $this->assertFileExists(storage_path('framework/down'));
        $data = json_decode(file_get_contents(storage_path('framework/down')), true);
        $this->assertNotNull($data);
        $this->assertArrayHasKey('template', $data);
        $this->assertStringContainsString('maintenance-banner', $data['template']);
        $this->assertStringContainsString('System Notice', $data['template']);
        $this->assertStringContainsString('The platform is currently under maintenance', $data['template']);

        Artisan::call('up');
        $this->assertFileDoesNotExist(storage_path('framework/maintenance.php'));
    }
}
