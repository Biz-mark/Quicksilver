<?php namespace BizMark\Quicksilver\Classes\Middleware;

use Backend\Facades\BackendAuth;
use BizMark\Quicksilver\Models\Settings;
use Closure;
use Exception;
use BizMark\Quicksilver\Classes\Contracts\Cache;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

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
     * @param Cache $cache
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

        if ($this->shouldCache($request, $response)) {
            $this->cache->cache($request, $response);
        }

        return $response;
    }

    /**
     * Determines whether the given request/response pair should be cached.
     *
     * @param Request $request
     * @param Response $response
     * @return bool
     */
    protected function shouldCache(Request $request, Response $response): bool
    {
        return !$this->isExclude($request)
            && $this->cache->shouldCache($request, $response)
            && !BackendAuth::check()
            && !$this->cache->hasCache($request);
    }

    protected function isExclude(Request $request): bool
    {
        return $request->is(Settings::instance()->getExcludeListPatterns());
    }
}
