<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function createApplication()
    {
        $cachedConfig = __DIR__.'/../bootstrap/cache/config.php';
        if (file_exists($cachedConfig)) {
            @unlink($cachedConfig);
        }

        return parent::createApplication();
    }
}
