<?php namespace BizMark\Quicksilver;

use BizMark\Quicksilver\Models\Settings;
use Event;
use BizMark\Quicksilver\Classes\Console\ClearCache;
use BizMark\Quicksilver\ReportWidgets\CacheStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Lang;
use System\Classes\PluginBase;
use Illuminate\Contracts\Http\Kernel;
use BizMark\Quicksilver\Classes\Contracts\Cache as PageCacheContract;
use BizMark\Quicksilver\Classes\Cache;
use BizMark\Quicksilver\Classes\Middleware\CacheResponse;
use System\Classes\SettingsManager;

/**
 * Quicksilver Plugin Information File
 *
 * This code is based on Laravel Page Cache package https://github.com/JosephSilber/page-cache by JosephSilber.
 * OctoberCMS integration and adaptation by Nick Khaetsky at Biz-Mark.
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
            'author'      => 'Nick Khaetsky, Biz-Mark',
            'icon'        => 'icon-bolt'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(PageCacheContract::class, Cache::class);

        $this->registerConsoleCommand('page-cache:clear', ClearCache::class);
    }

    /**
     * Boot method, called right before the request route.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->app[Kernel::class]->prependMiddleware(CacheResponse::class);

        Event::listen('cache:cleared', static function (): void {
            Artisan::call('page-cache:clear');
        });
    }

    /**
     * Returns the array with report widgets for the dashboard.
     *
     * @return array
     */
    public function registerReportWidgets(): array
    {
        return [
            CacheStatus::class => [
                'label'   => 'bizmark.quicksilver::lang.reportwidget.cachestatus.name',
                'context' => 'dashboard'
            ],
        ];
    }

    /**
     * @return array
     */
    public function registerSettings(): array
    {
        return [
            'options' => [
                'label'       => 'bizmark.quicksilver::lang.settings.label',
                'description' => 'bizmark.quicksilver::lang.settings.description',
                'class'       => Settings::class,
                'category'    => SettingsManager::CATEGORY_CMS
            ]
        ];
    }
}
