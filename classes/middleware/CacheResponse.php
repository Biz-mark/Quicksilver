<?php namespace BizMark\Quicksilver\Classes\Middleware;

use Config;
use Closure;
use Exception;
use BackendAuth;

use BizMark\Quicksilver\Classes\Cache;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CacheResponse
{
    /**
     * The cache instance.
     *
     * @var Cache
     */
    protected $cache;

    /**
     * Constructor.
     *
     * @var Cache $cache
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws Exception
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($this->shouldCache($request, $response) && !$this->cache->hasCache($request)) {
            $this->cache->cache($request, $response);
        }

        return $response;
    }

    /**
     * Determines whether the given request/response pair should be cached.
     *
     * @param Request $request
     * @param $response
     * @return bool
     */
    protected function shouldCache(Request $request, $response)
    {
        return $request->isMethod('GET')
            && $request->getQueryString() == null
            && $response->getStatusCode() == 200
            && BackendAuth::check() == false
            && !strpos($request->getUri(), Config::get('cms.backendUri', 'backend'));
    }
}
