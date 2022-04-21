<?php namespace BizMark\Quicksilver\Models;

use Model;

/**
 * @method static mixed get(string $key, mixed $default)
 */
class Settings extends Model
{
    /**
     * @var array implement these behaviors
     */
    public $implement = [
        \System\Behaviors\SettingsModel::class
    ];

    /**
     * @var string settingsCode unique to this model
     */
    public $settingsCode = 'bizmark_quicksilver_settings';

    /**
     * @var string settingsFields configuration
     */
    public $settingsFields = 'fields.yaml';
}
