<?php namespace BizMark\Quicksilver\ReportWidgets;

use BizMark\Quicksilver\Classes\Contracts\Cache;
use Lang;
use File;
use Flash;
use Artisan;
use Backend\Classes\ReportWidgetBase;

/**
 * Class CacheStatus
 * @package BizMark\Quicksilver\ReportWidgets
 */
class CacheStatus extends ReportWidgetBase
{
    /**
     * @var array
     */
    protected $pages = [];

    /**
     * @var array
     */
    protected $october = [];

    /**
     * @return string
     * @throws \BizMark\Quicksilver\Classes\Exceptions\CacheDirectoryPathNotSetException
     */
    public function render(): string
    {
        $this->calculateCacheData();
        return $this->makePartial('wrapper');
    }

    /**
     * @return array
     * @throws \BizMark\Quicksilver\Classes\Exceptions\CacheDirectoryPathNotSetException
     */
    protected function renderWidget(): array
    {
        $this->calculateCacheData();
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
    protected function getFolderStats($path): array
    {
        $allFiles  = File::allFiles($path);
        $file_size = $this->calcAllFilesSize($allFiles);

        return [
            'total'  => count($allFiles),
            'weight' => number_format($file_size / 1048576,2)
        ];
    }

    /**
     * Clears only cached pages by Quicksilver
     *
     * @return array
     */
    public function onClearPageCache(): array
    {
        Artisan::call('page-cache:clear');

        Flash::success(Lang::get('bizmark.quicksilver::lang.reportwidget.cachestatus.flash.pages_cleared_success'));
        return $this->renderWidget();
    }

    /**
     * Clears all cache that was created by October and Quicksilver
     * @return array
     */
    public function onClearAllCache(): array
    {
        Artisan::call('cache:clear');

        Flash::success(Lang::get('bizmark.quicksilver::lang.reportwidget.cachestatus.flash.all_cleared_success'));
        return $this->renderWidget();
    }

    /**
     * Calculate all files size in array
     *
     * @param \Symfony\Component\Finder\SplFileInfo[] $files
     * @return int
     */
    private function calcAllFilesSize(array $files): int
    {
        $bytes = 0;
        foreach($files as $file) {
            $bytes += $file->getSize();
        }

        return $bytes;
    }

    /**
     * @throws \BizMark\Quicksilver\Classes\Exceptions\CacheDirectoryPathNotSetException
     */
    private function calculateCacheData(): void
    {
        $cacheManager = app(Cache::class);

        $this->pages   = $this->getFolderStats($cacheManager->getCachePath());
        $this->october = $this->getFolderStats(storage_path('cms'));
    }
}
