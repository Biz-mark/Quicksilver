<?php namespace BizMark\Quicksilver\Models;

use Illuminate\Support\Facades\Cache;
use October\Rain\Database\Model;

/**
 * Class Settings
 * @package BizMark\Quicksilver\Models
 */
class Settings extends Model
{
    /**
     *
     */
    public const CACHE_KEY = 'bizmark.quicksilver.setting';

    /**
     * @var string[]
     */
    public $implement = [
        'System.Behaviors.SettingsModel',
    ];

    /**
     * @var string
     */
    public $settingsCode = 'bizmark_quicksilver_settings';

    /**
     * @var string
     */
    public $settingsFields = 'fields.yaml';

    /**
     * @return array
     */
    public static function getExcludeListPatterns(): array
    {
        return \array_map(static function(array $row): string {
            return $row['url_pattern'];
        }, (array) Settings::instance()->get('exclude', []));
    }
}
