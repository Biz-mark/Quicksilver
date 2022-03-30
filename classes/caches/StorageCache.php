<?php namespace BizMark\Quicksilver\Classes\Caches;

use Storage;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use BizMark\Quicksilver\Classes\Contracts\Quicksilver;

/**
 * StorageCache class
 * @package BizMark\Quicksilver\Classes\Caches
 * @author Nick Khaetsky, Biz-Mark
 */
class StorageCache extends AbstractCache
{
    /**
     * Page cache directory name inside system storage.
     *
     * @var string
     */
    private string $cacheDirectory = 'page-cache';

    public function get(Request $request): Response
    {
        return new Response('', 200);
    }

    public function store(Request $request, Response $response): Quicksilver
    {
        if (!Storage::exists($this->cacheDirectory)) {
            Storage::makeDirectory($this->cacheDirectory);
        }

        // Get request full route. /demo/acme/page
        $requestedRoute = $request->path();
        $pageName = basename($requestedRoute); // TODO: Consider query strings

        // File path information
        $fileExtension = $this->getResponseContentType($response);
        $fileName = (empty($pageName) ? 'qs__index__qs' : $pageName) . $fileExtension;
        $filePath = $this->cacheDirectory.DIRECTORY_SEPARATOR.dirname($requestedRoute);

        if (!Storage::exists($filePath)) {
            Storage::makeDirectory($filePath);
        }

        Storage::put($filePath.DIRECTORY_SEPARATOR.$fileName, $response->getContent());

        return $this;
    }

    public function has(Request $request): bool
    {
        return false;
    }

    public function forget(?string $slug): bool
    {

    }

    public function clear(?string $path): bool
    {

    }

    protected function getResponseContentType(Response $response): string
    {
        $responseHeaders = $response->headers;
        if (!$responseHeaders->has('content-type')) {
            return '.html';
        }

        $contentTypeBag = explode('|', $responseHeaders->get('content-type'));
        $contentType = array_shift($contentTypeBag);

        switch ($contentType) {
            case 'application/json':
                return '.json';
            case 'application/atom+xml':
            case 'application/xml':
                return '.xml';
            case 'text/plain':
                return '.txt';
            case 'text/html':
            default:
                return '.html';
        }
    }
}
