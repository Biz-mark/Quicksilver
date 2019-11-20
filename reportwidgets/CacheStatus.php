<?php namespace BizMark\Quicksilver\ReportWidgets;

use Lang;
use File;
use Flash;
use Artisan;
use Backend\Classes\ReportWidgetBase;

class CacheStatus extends ReportWidgetBase
{
    public function render()
    {
        return $this->makePartial('wrapper');
    }

    protected function renderWidget()
    {
        return [
            '#cachestatuswrap' => $this->makePartial('widget')
        ];
    }

    /**
     * Small helper method to gather some stats from given path
     *
     * @param $path
     * @return array
     */
    protected function getFolderStats($path)
    {
        $file_size = 0;
        foreach(File::allFiles($path) as $file) {
            $file_size += $file->getSize();
        }
        return [
            'total' => count(File::allFiles($path)),
            'weight' => number_format($file_size / 1048576,2)
        ];
    }

    /**
     * Clears only cached pages by Quicksilver
     *
     * @return array
     */
    public function onClearPageCache()
    {
        Artisan::call('page-cache:clear');
        Flash::success(Lang::get('bizmark.quicksilver::lang.reportwidget.cachestatus.flash.pages_cleared_success'));
        return $this->renderWidget();
    }

    /**
     * Clears all cache that was created by October and Quicksilver
     */
    public function onClearAllCache()
    {
        Artisan::call('cache:clear');
        Artisan::call('page-cache:clear');
        Flash::success(Lang::get('bizmark.quicksilver::lang.reportwidget.cachestatus.flash.all_cleared_success'));
        return $this->renderWidget();
    }
}