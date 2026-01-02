<?php namespace BizMark\Quicksilver\Classes\Contracts;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Quicksilver interface.
 *
 * Defines the contract for cache storage implementations.
 *
 * @package BizMark\Quicksilver\Classes\Contracts
 * @author Nick Khaetsky, Biz-Mark
 */
interface Quicksilver
{
    /**
     * Determine whether a cached version of the request exists.
     *
     * @param Request $request
     * @return bool
     */
    public function has(Request $request): bool;

    /**
     * Determine whether the given request and response are eligible for caching.
     *
     * @param Request  $request
     * @param Response $response
     * @return bool
     */
    public function validate(Request $request, Response $response): bool;

    /**
     * Store the response in the cache.
     *
     * @param Request  $request
     * @param Response $response
     * @return self
     */
    public function store(Request $request, Response $response): self;

    /**
     * Retrieve a cached response for the given request.
     *
     * @param Request $request
     * @return Response
     */
    public function get(Request $request): Response;

    /**
     * Remove a cached entry by its path.
     *
     * @param string $path
     * @return bool
     */
    public function forget(string $path): bool;

    /**
     * Clear the entire cache storage.
     *
     * @return bool
     */
    public function clear(): bool;
}
