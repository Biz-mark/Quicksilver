<?php namespace BizMark\Quicksilver\Classes\Contracts;

use BizMark\Quicksilver\Classes\Exceptions\CacheDirectoryPathNotSetException;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Interface Cache
 * @package BizMark\Quicksilver\Classes\Contracts
 */
interface Cache
{
    /**
     * Sets the directory in which to store the cached pages.
     *
     * @param  string  $path
     * @return self
     */
    public function setCachePath(string $path): self;

    /**
     * Gets the path to the cache directory.
     *
     * @return string
     * @throws CacheDirectoryPathNotSetException
     */
    public function getCachePath(): string;

    /**
     * Caches the given response if we determine that it should be cache.
     *
     * @param Request $request
     * @param Response $response
     * @return self
     * @throws Exception
     */
    public function cacheIfNeeded(Request $request, Response $response): self;

    /**
     * Determines whether the given request/response pair should be cached.
     *
     * @param Request $request
     * @param Response $response
     * @return bool
     * @throws Exception
     */
    public function shouldCache(Request $request, Response $response): bool;

    /**
     * Check if file already cached
     * @param Request $request
     * @return bool
     * @throws Exception
     */
    public function hasCache(Request $request): bool;

    /**
     * Cache the response to a file.
     *
     * @param Request $request
     * @param Response $response
     * @return self
     * @throws Exception
     */
    public function cache(Request $request, Response $response): self;

    /**
     * Remove the cached file for the given slug.
     *
     * @param string|null $slug
     * @return bool
     * @throws Exception
     */
    public function forget(?string $slug): bool;

    /**
     * Fully clear the cache directory.
     *
     * @return bool
     * @throws Exception
     */
    public function clear(): bool;


}
