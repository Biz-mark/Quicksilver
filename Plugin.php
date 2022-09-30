<?php namespace BizMark\Quicksilver;

use Config;
use System\Classes\PluginBase;
use Illuminate\Contracts\Http\Kernel;

use BizMark\Quicksilver\Console\Clear;
use BizMark\Quicksilver\Models\Settings;
use BizMark\Quicksilver\Classes\Caches\StorageCache;
use BizMark\Quicksilver\Classes\Contracts\Quicksilver;
use BizMark\Quicksilver\Classes\Middlewares\QuicksilverMiddleware;
use BizMark\Quicksilver\ReportWidgets\QuicksilverClear;

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
            'name' => 'bizmark.quicksilver::lang.plugin.name',
            'description' => 'bizmark.quicksilver::lang.plugin.description',
            'author' => 'Biz-Mark, Nick Khaetsky',
            'icon' => 'icon-bolt'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Quicksilver::class, StorageCache::class);

        $this->registerConsoleCommand('quicksilver:clear', Clear::class);
    }

    /**
     * Boot method, called right before the request route.
     *
     * @return void
     */
    public function boot()
    {
        // Set quicksilver filesystem driver
        $quicksilverDisks = Config::get('bizmark.quicksilver::disks');
        foreach ($quicksilverDisks as $name => $options) {
            if (!empty($options['driver'])) {
                Config::set('filesystems.disks.' . $name, $options);
            }
        }

        // Prepend Quicksilver middleware
        $this->app[Kernel::class]->prependMiddleware(QuicksilverMiddleware::class);
    }

    /**
     * registerReportWidgets registers any report widgets provided by this plugin.
     *
     * @return array
     */
    public function registerReportWidgets()
    {
        return [
            QuicksilverClear::class => [
                'label' => 'bizmark.quicksilver::lang.reportwidget.label',
                'context' => 'dashboard'
            ],
        ];
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
                'label' => 'bizmark.quicksilver::lang.settings.label',
                'description' => 'bizmark.quicksilver::lang.settings.description',
                'class' => Settings::class,
                'icon' => 'icon-cog',
                'category' => 'bizmark.quicksilver::lang.plugin.name'
            ]
        ];
    }
}
