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
     * Allowed file types
     * Supporting basic web pages, api responses and robots
     */
    protected const ALLOWED_TYPES = [
        'html',
        'htm',
        'json',
        'rss',
        'xml',
        'txt'
    ];

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
        $isRequestAccess      = $request->isMethod('GET');
        $isRequestQueryAccess = $response->getStatusCode() === 200;
        $isBackendUri         = !Str::contains($request->getUri(), Config::get('cms::backendUri', 'backend'));
        $isNotAssetsCombined  = !Str::contains($request->getUri(), $request->getSchemeAndHttpHost() . '/combine/');

        return $isRequestAccess && $isRequestQueryAccess && $isBackendUri && $isNotAssetsCombined;
    }

    /**
     * @inheritDoc
     */
    public function cache(Request $request, Response $response): PageCacheContract
    {
        [$path, $file] = $this->getDirectoryAndFileNames($request, $response);

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
        $matchingFiles = $this->files->glob($this->getCachePath($this->aliasFilename($slug).'[q_*'));
        return $this->files->delete($matchingFiles);
    }

    /**
     * @inheritDoc
     */
    public function clear(?string $path = null): bool
    {
        return $this->files->cleanDirectory($this->getCachePath($path));
    }

    /**
     * Join the given paths together by the system's separator.
     *
     * @param  string[] $paths
     * @return string
     */
    protected function join(array $paths)
    {
        $trimmed = array_map(static function (?string $path): string {
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
    protected function getDirectoryAndFileNames(Request $request, Response $response): array
    {
        $requestPath = ltrim($request->getPathInfo(), '/');

        $segments = $requestPath === "" ? [''] : array_filter(explode('/', $requestPath));

        // Need args directly from the server request. Don't use parse_url because it re-orders args and
        // web servers can't handle without additional modules probably unavailable on default installs
        // where this plugin adds most value (i.e. Cannot configure true cache)
        $queryString = $_SERVER['QUERY_STRING'] ? $_SERVER['QUERY_STRING'] : null;

        $extension = $this->getFileExtension($response, $requestPath);

        // We place query string in bracket and prefix q_ even if empty to make it easy to configure web server
        // todo consider method prefix and differentiate ajax requests.
        $file = $this->aliasFilename(array_pop($segments)) . "[q_$queryString].$extension";

        return [
            $this->getCachePath(implode('/',$segments)),
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
        return in_array($filename,['','/']) ? 'pc__index__pc' : $filename;
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

    /**
     * Choose a file extension compatible with the content-type or explicitly declared extension
     *
     *
     * @param Response $response
     * @param string $requestPath
     *
     * @return string
     */
    protected function getFileExtension(Response $response, $requestPath)
    {
        // Check if request path ends with a valid file extension
        $existingExtension = explode(".", $requestPath);
        $existingExtension = strtolower(array_pop($existingExtension));
        if (in_array($existingExtension, self::ALLOWED_TYPES)) {
            return $existingExtension;
        }

        // Determine the file extension from content
        $contentType = $response->headers->get('content-type');

        // Some very loose checks
        // Test RSS before XML
        if (str_contains($contentType, "rss")) {
            return 'rss';
        }

        if (str_contains($contentType, "xml")) {
            return 'xml';
        }

        if (str_contains($contentType, "json") || str_contains($contentType, "x-javascript")) {
            return 'json';
        }

        if (str_contains($contentType, "html")) {
            return 'html';
        }

        if (str_contains($contentType, "text/plain")) {
            return 'txt';
        }

        return false;
    }
}
