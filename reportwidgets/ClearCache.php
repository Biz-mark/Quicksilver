<?php

namespace BizMark\Quicksilver\ReportWidgets;

use Backend\Classes\ReportWidgetBase;
use Artisan;
use Flash;
use Lang;

class ClearCache extends ReportWidgetBase
{
    public function render()
    {
        return $this->makePartial('widget');
    }

    public function onClearCache() {
        Artisan::call('page-cache:clear');
        Flash::success(e(trans('bizmark.quicksilver::lang.reportwidgets.clearcache.success')));
    }
}

?>
