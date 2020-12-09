<?php namespace BizMark\Quicksilver\Classes\Exceptions;


/**
 * Class CacheDirectoryPathNotSetException
 * @package BizMark\Quicksilver\Classes\Exceptions
 */
class CacheDirectoryPathNotSetException extends \Exception
{
    /**
     * @var string
     */
    protected $message = 'Cache path not set.';
}
