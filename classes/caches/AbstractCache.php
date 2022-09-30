<?php namespace BizMark\Quicksilver\Classes\Caches;

use App, Str, Event;
use Illuminate\Http\Request;
use Backend\Facades\BackendAuth;
use Symfony\Component\HttpFoundation\Response;

use BizMark\Quicksilver\Models\Settings;
use BizMark\Quicksilver\Classes\Contracts\Quicksilver;

/**
 * AbstractCache class
 * @package BizMark\Quicksilver\Classes\Caches
 * @author Nick Khaetsky, Biz-Mark
 */
abstract class AbstractCache implements Quicksilver
{
    /**
     * Event name called before request is validated
     */
    const EVENT_IS_REQUEST_VALID = 'bizmark.quicksilver.is_request_valid';

    /**
     * Event name called before response is validated
     */
    const EVENT_IS_RESPONSE_VALID = 'bizmark.quicksilver.is_response_valid';

    /**
     * Validate if a pair of request and response should be cached.
     *
     * @param Request $request
     * @param Response $response
     * @return bool
     */
    public function validate(Request $request, Response $response): bool
    {
        if (!$this->isRequestValid($request)) {
            return false;
        }

        if (!$this->isResponseValid($response)) {
            return false;
        }

        return true;
    }

    /**
     * Check that incoming request is valid to be cached.
     *
     * @param Request $request
     * @return bool
     */
    protected function isRequestValid(Request $request): bool
    {
        // Check if request coming from administrator
        if (App::runningInBackend() || BackendAuth::check()) {
            return false;
        }

        // Check if request is for October CMS asset combiner
        if (Str::startsWith($request->path(), 'combine')) {
            return false;
        }

        // Check if request is coming from October frontend framework
        if ($request->hasHeader('X-OCTOBER-REQUEST-HANDLER') ||
            $request->hasHeader('X-OCTOBER-REQUEST-PARTIAL')) {
            return false;
        }

        // Check if we had to cache request with query strings
        $isQueryShouldCache = Settings::get('cache_query_strings', false);
        if (!empty($request->getQueryString()) && !$isQueryShouldCache) {
            return false;
        }

        $excludedPaths = Settings::get('exclude_paths', []);
        if (!empty($excludedPaths)) {
            foreach ($excludedPaths as $path) {
                if ($request->is($path)) {
                    return false;
                }
            }
        }

        // Support custom validation
        if (Event::fire(self::EVENT_IS_REQUEST_VALID, [$request]) === false) {
            return false;
        }

        return true;
    }

    /**
     * Check that prepared request is valid to be cached.
     *
     * @param Response $response
     * @return bool
     */
    protected function isResponseValid(Response $response): bool
    {
        // Check that response is succeeded
        if ($response->getStatusCode() !== 200) {
            return false;
        }

        // Support custom validation
        if (Event::fire(self::EVENT_IS_RESPONSE_VALID, [$response]) === false) {
            return false;
        }

        return true;
    }
}
