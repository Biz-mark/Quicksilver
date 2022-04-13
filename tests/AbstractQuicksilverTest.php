<?php namespace BizMark\Quicksilver\Test;

use PluginTestCase;
use System\Classes\PluginManager;

abstract class AbstractQuicksilverTest extends PluginTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        PluginManager::instance()->registerAll(true);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        PluginManager::instance()->unregisterAll();
    }
}
