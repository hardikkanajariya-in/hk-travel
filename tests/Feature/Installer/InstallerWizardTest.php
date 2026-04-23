<?php

namespace Tests\Feature\Installer;

use App\Core\Installer\InstallationState;
use Tests\TestCase;

class InstallerWizardTest extends TestCase
{
    private ?string $originalLockContents = null;

    protected function setUp(): void
    {
        parent::setUp();

        $state = app(InstallationState::class);
        $lockPath = $state->lockPath();

        if (file_exists($lockPath)) {
            $this->originalLockContents = file_get_contents($lockPath) ?: null;
            $state->reset();
        }
    }

    protected function tearDown(): void
    {
        $state = app(InstallationState::class);
        $lockPath = $state->lockPath();

        if ($this->originalLockContents !== null) {
            file_put_contents($lockPath, $this->originalLockContents);
        } elseif (file_exists($lockPath)) {
            @unlink($lockPath);
        }

        parent::tearDown();
    }

    public function test_installer_wizard_does_not_expose_personal_admin_defaults(): void
    {
        $response = $this->get(route('install.welcome'));

        $response->assertOk();
        $response->assertDontSee('Hardik Kanajariya');
        $response->assertDontSee('hardik@hardikkanajariya.in');
        $response->assertDontSee('Nud@#38648');
    }
}
