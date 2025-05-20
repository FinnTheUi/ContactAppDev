<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;
use Tests\CreatesApplication;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }

    protected function setUp(): void
    {
        parent::setUp();
        
        // Don't disable exception handling by default
        // Each test can explicitly disable it if needed
        // $this->withoutExceptionHandling();
        
        $this->withSession(['_token' => 'test-token']);
        
        // Begin a new transaction before each test
        DB::beginTransaction();
    }
    
    protected function tearDown(): void
    {
        // Roll back transaction after each test
        DB::rollBack();
        
        parent::tearDown();
    }
}
