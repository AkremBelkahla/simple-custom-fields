<?php

use PHPUnit\Framework\TestCase;
use Brain\Monkey;

class WordpressIntegrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Monkey\setUp();
    }

    protected function tearDown(): void
    {
        Monkey\tearDown();
        parent::tearDown();
    }

    public function testAdminInitHook()
    {
        Monkey\Actions\expectAdded('admin_init')
            ->once()
            ->with(Mockery::type('callable'));

        // Code qui devrait ajouter le hook admin_init
        do_action('admin_init');
    }
}
