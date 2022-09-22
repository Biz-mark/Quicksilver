<?php namespace BizMark\Quicksilver\Tests\Unit\Classes\Caches;

use Config, PluginTestCase;
use BizMark\Quicksilver\Classes\Caches\StorageCache;

class AbstractCacheTest extends PluginTestCase
{
    public function testGetDefaultHeaders()
    {
        // We're using storage cache to test method of abstract cache (can't instantiate the abstract one)
        $storageCache = new StorageCache();
        $this->assertEquals(['Cache-Control' => 'public, max-age=7200'], $storageCache->getDefaultHeaders());

        Config::set('bizmark.quicksilver::defaultHeaders', ['Cache-Control' => 'public, max-age=180']);
        $this->assertEquals(['Cache-Control' => 'public, max-age=180'], $storageCache->getDefaultHeaders());

        Config::set('bizmark.quicksilver::defaultHeaders', ['Cache-Control' => '']);
        $this->assertEquals([], $storageCache->getDefaultHeaders());
    }
}
