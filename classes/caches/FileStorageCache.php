<?php namespace BizMark\Quicksilver\Classes\Caches;

use App;
use Illuminate\Http\Request;
use Backend\Facades\BackendAuth;
use Symfony\Component\HttpFoundation\Response;
use BizMark\Quicksilver\Classes\Contracts\Quicksilver;

/**
 * FileStorageCache class
 * @package BizMark\Quicksilver\Classes\Caches
 * @author Nick Khaetsky, Biz-Mark
 */
class FileStorageCache implements Quicksilver
{
    public function hasCache(Request $request): bool
    {
        return false;
    }

    public function shouldCache(Request $request, Response $response): bool
    {
        // Check if request coming from administrator
        if (App::runningInBackend() ||
            BackendAuth::check()) {
            return false;
        }

        return true;
    }

    public function store(Request $request, Response $response): Quicksilver
    {
    }

    public function get(Request $request): Response
    {
        return new Response('', 200);
    }

    public function forget(?string $slug): bool
    {
    }

    public function clear(?string $path): bool
    {
    }
}
