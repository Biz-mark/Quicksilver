<?php namespace BizMark\Quicksilver\Classes\Caches;

use App, Str, Event;
use Illuminate\Http\Request;
use Backend\Facades\BackendAuth;
use Symfony\Component\HttpFoundation\Response;

use BizMark\Quicksilver\Models\Settings;
use BizMark\Quicksilver\Classes\Contracts\Quicksilver;

/**
 * AbstractCache class.
 *
 * Base class for cache validation logic.
 *
 * @package BizMark\Quicksilver\Classes\Caches
 * @author Nick Khaetsky, Biz-Mark
 */
abstract class AbstractCache implements Quicksilver
{
    /**
     * Event name triggered before validating the request.
     */
    const EVENT_IS_REQUEST_VALID = 'bizmark.quicksilver.is_request_valid';

    /**
     * Event name triggered before validating the response.
     */
    const EVENT_IS_RESPONSE_VALID = 'bizmark.quicksilver.is_response_valid';

    /**
     * Determines whether a request/response pair can be cached.
     *
     * @param Request  $request
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
     * Checks whether the incoming request is eligible for caching.
     *
     * @param Request $request
     * @return bool
     */
    protected function isRequestValid(Request $request): bool
    {
        // Reject requests coming from the backend or authenticated administrators
        if (App::runningInBackend() || BackendAuth::check()) {
            return false;
        }

        // Reject requests for October CMS asset combiner
        if (Str::startsWith($request->path(), 'combine')) {
            return false;
        }

        // Reject requests originating from the October CMS frontend framework (AJAX/partials)
        if (
            $request->hasHeader('X-OCTOBER-REQUEST-HANDLER') ||
            $request->hasHeader('X-OCTOBER-REQUEST-PARTIAL')
        ) {
            return false;
        }

        // Reject requests with query strings if query caching is disabled
        $isQueryShouldCache = Settings::get('cache_query_strings', false);
        if (!empty($request->getQueryString()) && !$isQueryShouldCache) {
            return false;
        }

        // Reject requests matching excluded paths
        $excludedPaths = Settings::get('exclude_paths', []);
        if (!empty($excludedPaths)) {
            foreach ($excludedPaths as $path) {
                if ($request->is($path)) {
                    return false;
                }
            }
        }

        // Allow custom request validation via event listeners
        if (Event::fire(self::EVENT_IS_REQUEST_VALID, [$request]) === false) {
            return false;
        }

        return true;
    }

    /**
     * Checks whether the response is eligible for caching.
     *
     * @param Response $response
     * @return bool
     */
    protected function isResponseValid(Response $response): bool
    {
        // Only cache successful responses
        if ($response->getStatusCode() !== 200) {
            return false;
        }

        // Allow custom response validation via event listeners
        if (Event::fire(self::EVENT_IS_RESPONSE_VALID, [$response]) === false) {
            return false;
        }

        return true;
    }
}
