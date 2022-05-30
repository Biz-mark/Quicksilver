<?php namespace BizMark\Quicksilver\ReportWidgets;

use Artisan, Flash, Input, Lang;
use Backend\Classes\ReportWidgetBase;

/**
 * Quicksilver Plugin Information File
 * @package BizMark\Quicksilver\ReportWidgets
 * @author Nick Khaetsky, Biz-Mark
 */
class QuicksilverClear extends ReportWidgetBase
{
    public function render()
    {
        return $this->makePartial('widget');
    }

    public function onClearSpecificQuicksilverPath()
    {
        $specificPath = Input::get('path');
        Artisan::call('quicksilver:clear', ['path' => $specificPath]);

        Flash::success(Lang::get('bizmark.quicksilver::lang.reportwidget.clear_specific', ['path' => $specificPath]));
    }

    public function onClearQuicksilver()
    {
        Artisan::call('quicksilver:clear');

        Flash::success(Lang::get('bizmark.quicksilver::lang.reportwidget.clear_all_paths'));
    }

    public function onClearAll()
    {
        Artisan::call('cache:clear');
        Artisan::call('quicksilver:clear');

        Flash::success(Lang::get('bizmark.quicksilver::lang.reportwidget.clear_all'));
    }
}
