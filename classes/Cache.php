<?php namespace BizMark\Quicksilver\Classes;

use BizMark\Quicksilver\Classes\Exceptions\CacheDirectoryPathNotSetException;
use Config;
use Exception;

use Illuminate\Filesystem\Filesystem;
use BizMark\Quicksilver\Classes\Contracts\Cache as PageCacheContract;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use October\Rain\Support\Str;

/**
 * Class Cache
 * @package BizMark\Quicksilver\Classes
 */
class Cache implements PageCacheContract
{
    /**
     * Page cache directory name
     *
     * @var string
     */
    protected const DIRECTORY = 'page-cache';

    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * The directory in which to store the cached pages.
     *
     * @var string|null
     */
    protected $cachePath = null;

    /**
     * Constructor.
     *
     * @var Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    /**
     * @inheritDoc
     */
    public function setCachePath(string $path): PageCacheContract
    {
        $this->cachePath = rtrim($path, '\/');

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCachePath(): string
    {
        $base = $this->cachePath ?: $this->getDefaultCachePath();

        if (is_null($base)) {
            throw new CacheDirectoryPathNotSetException;
        }

        return $this->join(array_merge([$base], func_get_args()));
    }

    /**
     * @inheritDoc
     */
    public function cacheIfNeeded(Request $request, Response $response): PageCacheContract
    {
        if ($this->shouldCache($request, $response)) {
            $this->cache($request, $response);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function shouldCache(Request $request, Response $response): bool
    {
        $isRequestAccess      = $request->isMethod('GET') && $request->getQueryString() === null;
        $isRequestQueryAccess = $response->getStatusCode() === 200 && $request->getQueryString() === null;
        $isBackendUri         = !Str::contains($request->getUri(), Config::get('cms::backendUri', 'backend'));

        return $isRequestAccess && $isRequestQueryAccess && $isBackendUri;
    }

    /**
     * @inheritDoc
     */
    public function hasCache(Request $request): bool
    {
        $cachePath = $this->getCachePath($request->getRequestUri() . '.html');
        return $this->files->exists($cachePath);
    }

    /**
     * @inheritDoc
     */
    public function cache(Request $request, Response $response): PageCacheContract
    {
        [$path, $file] = $this->getDirectoryAndFileNames($request);

        $this->files->makeDirectory($path, 0775, true, true);

        $this->files->put(
            $this->join([$path, $file]),
            $response->getContent(),
            true
        );

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function forget(?string $slug): bool
    {
        return $this->files->delete($this->getCachePath($slug.'.html'));
    }

    /**
     * @inheritDoc
     */
    public function clear(): bool
    {
        return $this->files->cleanDirectory($this->getCachePath());
    }

    /**
     * Join the given paths together by the system's separator.
     *
     * @param  string[] $paths
     * @return string
     */
    protected function join(array $paths)
    {
        $trimmed = array_map(static function (string $path): string {
            return trim($path, '/');
        }, $paths);

        return $this->matchRelativity(
            $paths[0], implode('/', array_filter($trimmed))
        );
    }

    /**
     * Makes the target path absolute if the source path is also absolute.
     *
     * @param  string  $source
     * @param  string  $target
     * @return string
     */
    protected function matchRelativity(string $source, string $target): string
    {
        return $source[0] === '/' ? '/' . $target : $target;
    }

    /**
     * Get the names of the directory and file.
     *
     * @param Request $request
     * @return array
     * @throws Exception
     */
    protected function getDirectoryAndFileNames(Request $request): array
    {
        $requestPath = ltrim($request->getPathInfo(), '/');
        $segments = explode('/', $requestPath);

        $file = $this->aliasFilename(array_pop($segments)).'.html';
        return [
            $this->getCachePath($requestPath),
            $file
        ];
    }

    /**
     * Alias the filename if necessary.
     *
     * @param  string|null  $filename
     * @return string
     */
    protected function aliasFilename(?string $filename): string
    {
        return $filename ?: 'pc__index__pc';
    }

    /**
     * Get the default path to the cache directory.
     *
     * @return string|null
     */
    protected function getDefaultCachePath(): ?string
    {
        return storage_path(static::DIRECTORY);
    }
}
