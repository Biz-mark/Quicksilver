<?php namespace BizMark\Quicksilver\Classes\Caches;

use Storage;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use BizMark\Quicksilver\Classes\Contracts\Quicksilver;

use BizMark\Quicksilver\Models\Settings;

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
     * Should we cache page with different query strings?
     *
     * @var bool
     */
    private bool $isQueryShouldCache;

    /**
     * StorageCache constructor.
     *
     * @return void
     */
    public function __construct() {
        $this->isQueryShouldCache = Settings::get('cache_query_strings', false);
    }

    /**
     * Get requested page from cache
     *
     * @param Request $request
     * @return Response
     */
    public function get(Request $request): Response
    {
        $fileInformation = $this->getFileInformation($request);
        return new Response(Storage::get($fileInformation['fullPath']), 200, [
            'Content-Type' => $fileInformation['contentType']
        ]);
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

        $fileInformation = $this->getFileInformation($request, $response);
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

        $fileInformation = $this->getFileInformation($request);
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
     * @param Request $request
     * @param Response|null $response
     * @return array
     */
    protected function getFileInformation(Request $request, Response $response = null): array
    {
        $path = $request->path();
        $headersBag = !empty($response) ? $response : $request;
        $pageName = $request->getMethod() . '.' . (!empty(basename($path)) ? basename($path) : 'qs__index__qs');

        if ($this->isQueryShouldCache) {
            if (!empty($request->all())) {
                $pageName .= '.' . urlencode(json_encode($request->all()));
            }
        }

        [$fileExtension, $contentType] = $this->getFileExtension($headersBag);
        $fileName = $pageName . $fileExtension;
        $filePath = $this->cacheDirectory;
        if ($pageName !== 'qs__index__qs') {
            $filePath = $this->cacheDirectory . DIRECTORY_SEPARATOR . dirname($path);
        }

        return [
            'fileExtension' => $fileExtension,
            'fileName' => $fileName,
            'dirname' => $filePath,
            'fullPath' => $filePath . DIRECTORY_SEPARATOR . $fileName,
            'contentType' => $contentType
        ];
    }

    /**
     * Get file extension from headers content type
     *
     * @param Response|Request $headersBag
     * @return array
     */
    protected function getFileExtension($headersBag = null): array
    {
        $headers = $headersBag->headers;
        if (empty($headers) || !$headers->has('content-type')) {
            return ['.html', 'text/html'];
        }

        $contentTypeBag = explode('|', $headers->get('content-type'));
        $contentType = array_shift($contentTypeBag);

        switch ($contentType) {
            case 'application/json':
                $fileFormat = '.json';
                break;
            case 'application/atom+xml':
            case 'application/xml':
                $fileFormat = '.xml';
                break;
            case 'text/plain':
                $fileFormat = '.txt';
                break;
            case 'text/html':
            default:
                $fileFormat = '.html';
                break;
        }

        return [$fileFormat, $contentType];
    }
}
