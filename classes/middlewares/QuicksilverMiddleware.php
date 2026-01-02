<?php namespace BizMark\Quicksilver\Classes\Middlewares;

use Closure;
use Illuminate\Http\Request;
use BizMark\Quicksilver\Classes\Contracts\Quicksilver;

/**
 * QuicksilverMiddleware class.
 *
 * Handles HTTP response caching using the Quicksilver cache layer.
 *
 * @package BizMark\Quicksilver\Classes\Middlewares
 * @author Nick Khaetsky, Biz-Mark
 */
class QuicksilverMiddleware
{
    /**
     * Quicksilver cache implementation.
     *
     * @var Quicksilver
     */
    protected $cache;

    /**
     * Create a new middleware instance.
     *
     * @param Quicksilver $cache
     */
    public function __construct(Quicksilver $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Process an incoming HTTP request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Return cached response if it exists
        if ($this->cache->has($request)) {
            return $this->cache->get($request);
        }

        // Handle the request and obtain the response
        $response = $next($request);

        // Store the response in cache if it is eligible
        if ($this->cache->validate($request, $response)) {
            $this->cache->store($request, $response);
        }

        return $response;
    }
}
