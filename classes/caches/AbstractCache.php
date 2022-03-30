<?php namespace BizMark\Quicksilver\Classes\Caches;

use App, Str;
use BizMark\Quicksilver\Models\Settings;
use Illuminate\Http\Request;
use Backend\Facades\BackendAuth;
use BizMark\Quicksilver\Classes\Contracts\Quicksilver;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractCache implements Quicksilver
{
    /**
     * Validate if a pair of request and response should be cached.
     *
     * @param Request $request
     * @param Response $response
     * @return bool
     */
    public function validate(Request $request, Response $response): bool
    {
        if (!$this->isNotSystemRoute($request)) {
            return false;
        }

        if (!$this->isNotExcludedRoute($request)) {
            return false;
        }

        if (!$this->isResponseChecked($response)) {
            return false;
        }

        return true;
    }

    /**
     * isNotSystemRoute checks if requested system route or
     * request coming from administrator.
     *
     * @param Request $request
     * @return bool
     */
    protected function isNotSystemRoute(Request $request): bool
    {
        // Check if request coming from administrator
        if (App::runningInBackend() || BackendAuth::check()) {
            return false;
        }

        // Check if request is for October CMS asset combiner
        if (Str::startsWith($request->path(), 'combine')) {
            return false;
        }

        return true;
    }

    /**
     * isNotExcludedRoute checks other system points to determine
     * if request should be cached or not.
     *
     * @param Request $request
     * @return bool
     */
    protected function isNotExcludedRoute(Request $request): bool
    {
        // TODO: Excluded routes logic
        // TODO: Fire event to check if there is any excluded route

        // Check if we had to cache request with query strings
        $isQueryShouldCache = Settings::get('cache_query_strings', false);
        if (!empty($request->getQueryString()) && !$isQueryShouldCache) {
            return false;
        }

        return true;
    }

    /**
     * isResponseChecked checks that response is correct
     * and valid to be cached.
     *
     * @param Response $response
     * @return bool
     */
    protected function isResponseChecked(Response $response): bool
    {
        // Check that response is succeeded
        if ($response->getStatusCode() !== 200) {
            return false;
        }

        return true;
    }
}
