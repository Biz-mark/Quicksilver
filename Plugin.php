<?php namespace BizMark\Quicksilver;

use Backend;
use System\Classes\PluginBase;
use Illuminate\Contracts\Http\Kernel;
use BizMark\Quicksilver\Classes\Middleware\CacheResponse;

/**
 * Quicksilver Plugin Information File
 *
 * LARAVEL PAGE CACHE PACKAGE INTEGRATION FOR OCTOBERCMS.
 * ORIGINAL PACKAGE IS https://github.com/JosephSilber/page-cache
 * ALL CREDITS GOES TO JosephSilber
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'bizmark.quicksilver::lang.plugin.name',
            'description' => 'bizmark.quicksilver::lang.plugin.description',
            'author'      => 'Joseph Silber, Nick Khaetsky, Biz-Mark',
            'icon'        => 'icon-bolt'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {
        $this->registerConsoleCommand('page-cache:clear', 'BizMark\Quicksilver\Classes\Console\ClearCache');
    }

    /**
     * Boot method, called right before the request route.
     */
    public function boot()
    {
        $this->app[Kernel::class]->pushMiddleware(CacheResponse::class);
    }
}
