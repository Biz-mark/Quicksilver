<?php namespace BizMark\Quicksilver\Test;

use Http;
use PluginTestCase;
use System\Classes\PluginManager;

class QuicksilverTest extends PluginTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        // Get the plugin manager
        $pluginManager = PluginManager::instance();

        // Register the plugins to make features like file configuration available
        $pluginManager->registerAll(true);

    }

    public function testBasicCache()
    {
        // TODO: Test quicksilver cache
    }

    public function tearDown(): void
    {
        parent::tearDown();

        // Get the plugin manager
        $pluginManager = PluginManager::instance();

        // Ensure that plugins are registered again for the next test
        $pluginManager->unregisterAll();
    }
}
