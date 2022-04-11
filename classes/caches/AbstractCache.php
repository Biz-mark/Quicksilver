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

        // TODO: Excluded routes logic
        // TODO: Excluded queries in specific routes logic
        // TODO: Fire event to check if there is any excluded route

        // Check if we had to cache request with query strings
        $isQueryShouldCache = Settings::get('cache_query_strings', false);
        if (!empty($request->getQueryString()) && !$isQueryShouldCache) {
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
        // TODO: Check if response has "combine" links and don't cache it
        //

        // Check that response is succeeded
        if ($response->getStatusCode() !== 200) {
            return false;
        }

        return true;
    }
}
