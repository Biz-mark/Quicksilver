<?php namespace BizMark\Quicksilver\Classes\Schedule;

use BizMark\Quicksilver\Classes\CacheCleaner;
use System\Classes\PluginManager;

class CheckScheduledPosts
{
    public static function check($schedule)
    {
        if (PluginManager::instance()->hasPlugin('RainLab.Blog')) {
            $schedule->call(function () {
                CacheCleaner::checkScheduledPosts();
            })->everyMinute();
        }
    }
}