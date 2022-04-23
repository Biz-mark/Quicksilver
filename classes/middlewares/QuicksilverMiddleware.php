<?php namespace BizMark\Quicksilver\Classes\Middlewares;

use Closure;
use Illuminate\Http\Request;
use BizMark\Quicksilver\Classes\Contracts\Quicksilver;

/**
 * CacheMiddleware middleware
 * @package BizMark\Quicksilver\Classes\Middlewares
 * @author Nick Khaetsky, Biz-Mark
 */
class QuicksilverMiddleware
{
    /**
     * The Quicksilver cache interface instance.
     *
     * @var Quicksilver
     */
    protected $cache;

    /**
     * Middleware constructor.
     *
     * @param Quicksilver $cache
     */
    public function __construct(Quicksilver $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($this->cache->has($request)) {
            return $this->cache->get($request);
        }

        $response = $next($request);

        if ($this->cache->validate($request, $response)) {
            $this->cache->store($request, $response);
        }

        return $response;
    }
}
