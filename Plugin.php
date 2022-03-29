<?php namespace BizMark\Quicksilver;

use Backend;
use BizMark\Quicksilver\Models\Settings;
use System\Classes\PluginBase;
use Illuminate\Contracts\Http\Kernel;

use BizMark\Quicksilver\Classes\Contracts\Quicksilver;
use BizMark\Quicksilver\Classes\Caches\FileStorageCache;
use BizMark\Quicksilver\Classes\Middlewares\QuicksilverMiddleware;

/**
 * Quicksilver Plugin Information File
 * @package BizMark\Quicksilver
 * @author Nick Khaetsky, Biz-Mark
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails(): array
    {
        return [
            'name'        => 'bizmark.quicksilver::lang.plugin.name',
            'description' => 'bizmark.quicksilver::lang.plugin.description',
            'author'      => 'Biz-Mark, Nick Khaetsky',
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
        $this->app->bind(Quicksilver::class, FileStorageCache::class);
    }

    /**
     * Boot method, called right before the request route.
     *
     * @return void
     */
    public function boot()
    {
        $this->app[Kernel::class]->prependMiddleware(QuicksilverMiddleware::class);
    }

    /**
     * registerSettings registers any back-end configuration links used by this plugin.
     *
     * @return array
     */
    public function registerSettings(): array
    {
        return [
            'settings' => [
                'label'       => 'bizmark.quicksilver::lang.settings.label',
                'description' => 'bizmark.quicksilver::lang.settings.description',
                'class'       => Settings::class,
                'icon'        => 'icon-cog',
                'category'    => 'bizmark.quicksilver::lang.plugin.name'
            ]
        ];
    }
}
