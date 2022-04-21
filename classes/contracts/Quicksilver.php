<?php namespace BizMark\Quicksilver\Classes\Contracts;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Quicksilver interface
 * @package BizMark\Quicksilver\Classes\Contracts
 * @author Nick Khaetsky, Biz-Mark
 */
interface Quicksilver
{
    /**
     * Check if file already cached
     *
     * @param Request $request
     * @return bool
     */
    public function has(Request $request): bool;

    /**
     * Determines whether the given request/response pair should be cached.
     *
     * @param Request $request
     * @param Response $response
     * @return bool
     */
    public function validate(Request $request, Response $response): bool;

    /**
     * Cache the response to a file.
     *
     * @param Request $request
     * @param Response $response
     * @return self
     */
    public function store(Request $request, Response $response): self;

    /**
     * Get response from cache
     *
     * @param Request $request
     * @return Response
     */
    public function get(Request $request): Response;

    /**
     * Remove the cached file for the given slug.
     *
     * @param string $path
     * @return bool
     */
    public function forget(string $path): bool;

    /**
     * Fully clear the cache directory.
     *
     * @return bool
     */
    public function clear(): bool;
}
