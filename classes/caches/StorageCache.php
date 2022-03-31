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

    /**
     * Get requested page from cache
     *
     * @param Request $request
     * @return Response
     */
    public function get(Request $request): Response
    {
        $fileInformation = $this->getFileInformation($request->path(), $request);
        return new Response(Storage::get($fileInformation['fullPath']), 200);
    }

    /**
     * Store response to storage cache
     *
     * @param Request $request
     * @param Response $response
     * @return Quicksilver
     */
    public function store(Request $request, Response $response): Quicksilver
    {
        if (!Storage::exists($this->cacheDirectory)) {
            Storage::makeDirectory($this->cacheDirectory);
        }

        $fileInformation = $this->getFileInformation($request->path(), $response);

        if (!Storage::exists($fileInformation['dirname'])) {
            Storage::makeDirectory($fileInformation['dirname']);
        }

        Storage::put($fileInformation['fullPath'], $response->getContent());

        return $this;
    }

    /**
     * Check if storage has request cached in storage
     *
     * @param Request $request
     * @return bool
     */
    public function has(Request $request): bool
    {
        if (!Storage::exists($this->cacheDirectory)) {
            return false;
        }

        $fileInformation = $this->getFileInformation($request->path(), $request);
        if (!Storage::exists($fileInformation['fullPath'])) {
            return false;
        }

        return true;
    }

    /**
     * Remove specific route from storage cache
     *
     * @param string $path
     * @return bool
     */
    public function forget(string $path): bool
    {
        return Storage::delete($path);
    }

    /**
     * Clear whole storage page cache
     *
     * @return bool
     */
    public function clear(): bool
    {
        return Storage::delete($this->cacheDirectory);
    }

    /**
     * Get requested file path as array with information
     *
     * @param string $path
     * @param $headersBag
     * @return string[]
     */
    protected function getFileInformation(string $path, $headersBag): array
    {
        $pageName = basename($path); // TODO: Consider query strings
        $fileName = (empty($pageName) ? 'qs__index__qs' : $pageName) . $this->getFileExtension($headersBag);
        $filePath = $this->cacheDirectory . DIRECTORY_SEPARATOR . dirname($path);

        return [
            'dirname' => $filePath,
            'fullPath' => $filePath . DIRECTORY_SEPARATOR . $fileName,
            'fileName' => $fileName
        ];
    }

    /**
     * Get file extension from headers content type
     *
     * @param Response|Request $headersBag
     * @return string
     */
    protected function getFileExtension($headersBag = null): string
    {
        $headers = $headersBag->headers;
        if (empty($headers) || !$headers->has('content-type')) {
            return '.html';
        }

        $contentTypeBag = explode('|', $headers->get('content-type'));
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
