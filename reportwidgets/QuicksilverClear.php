<?php namespace BizMark\Quicksilver\ReportWidgets;

use Artisan, Flash, Input, Lang;
use Backend\Classes\ReportWidgetBase;

/**
 * Quicksilver cache cleaner report widget.
 *
 * Provides UI actions for clearing Quicksilver cache entries.
 *
 * @package BizMark\Quicksilver\ReportWidgets
 * @author Nick Khaetsky, Biz-Mark
 */
class QuicksilverClear extends ReportWidgetBase
{
    /**
     * Render the report widget.
     *
     * @return mixed
     * @throws \SystemException
     */
    public function render(): mixed
    {
        return $this->makePartial('widget');
    }

    /**
     * Clear a specific Quicksilver cache path provided by the user.
     *
     * @return void
     */
    public function onClearSpecificQuicksilverPath(): void
    {
        $specificPath = Input::get('path');
        Artisan::call('quicksilver:clear', ['path' => $specificPath]);

        Flash::success(Lang::get(
            'bizmark.quicksilver::lang.reportwidget.clear_specific',
            ['path' => $specificPath]
        ));
    }

    /**
     * Clear all Quicksilver cached pages.
     *
     * @return void
     */
    public function onClearQuicksilver(): void
    {
        Artisan::call('quicksilver:clear');

        Flash::success(
            Lang::get('bizmark.quicksilver::lang.reportwidget.clear_all_paths')
        );
    }

    /**
     * Clear both Quicksilver cache and the system cache.
     *
     * @return void
     */
    public function onClearAll(): void
    {
        Artisan::call('cache:clear');
        Artisan::call('quicksilver:clear');

        Flash::success(
            Lang::get('bizmark.quicksilver::lang.reportwidget.clear_all')
        );
    }
}
