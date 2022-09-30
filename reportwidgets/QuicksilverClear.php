<?php namespace BizMark\Quicksilver\ReportWidgets;

use Artisan, Flash, Input, Lang;
use Backend\Classes\ReportWidgetBase;

/**
 * Quicksilver cleaner reportwidget
 * @package BizMark\Quicksilver\ReportWidgets
 * @author Nick Khaetsky, Biz-Mark
 */
class QuicksilverClear extends ReportWidgetBase
{
    /**
     * Render widget
     *
     * @return mixed
     * @throws \SystemException
     */
    public function render(): mixed
    {
        return $this->makePartial('widget');
    }

    /**
     * Clear specific quicksilver path provided by user
     *
     * @return void
     */
    public function onClearSpecificQuicksilverPath(): void
    {
        $specificPath = Input::get('path');
        Artisan::call('quicksilver:clear', ['path' => $specificPath]);

        Flash::success(Lang::get('bizmark.quicksilver::lang.reportwidget.clear_specific', [
            'path' => $specificPath
        ]));
    }

    /**
     * Clear all quicksilver cache
     *
     * @return void
     */
    public function onClearQuicksilver(): void
    {
        Artisan::call('quicksilver:clear');

        Flash::success(Lang::get('bizmark.quicksilver::lang.reportwidget.clear_all_paths'));
    }

    /**
     * Clear all quicksilver cache and system cache
     *
     * @return void
     */
    public function onClearAll(): void
    {
        Artisan::call('cache:clear');
        Artisan::call('quicksilver:clear');

        Flash::success(Lang::get('bizmark.quicksilver::lang.reportwidget.clear_all'));
    }
}
