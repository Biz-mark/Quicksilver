<?php namespace BizMark\Quicksilver\Classes\Caches;

use Storage, Config;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;
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
     * Default index file name
     */
    const INDEX_NAME = 'qs_index_qs';

    /**
     * Should we cache page with different query strings?
     *
     * @var bool
     */
    private bool $isQueryShouldCache;

    /**
     * Default Quicksilver Storage driver
     *
     * @var \Illuminate\Contracts\Filesystem\Filesystem|Storage
     */
    private $storageDisk;

    /**
     * StorageCache constructor.
     *
     * @return void
     */
    public function __construct() {
        $this->isQueryShouldCache = Settings::get('cache_query_strings', false);
        $this->storageDisk = Storage::disk(Config::get('bizmark.quicksilver::default'));
    }

    /**
     * Get requested page from cache
     *
     * @param Request $request
     * @return Response
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function get(Request $request): Response
    {
        $fileInformation = $this->getFileInformation($request);
        return new Response($this->storageDisk->get($fileInformation['path']), 200, [
            'Content-Type' => $fileInformation['mimeType']
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
        if (!$this->storageDisk->exists('/')) {
            $this->storageDisk->makeDirectory('/');
        }

        $fileInformation = $this->getFileInformation($request, $response);
        if (!$this->storageDisk->exists($fileInformation['directory'])) {
            $this->storageDisk->makeDirectory($fileInformation['directory']);
        }

        $this->storageDisk->put($fileInformation['path'], $response->getContent());

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
        $fileInformation = $this->getFileInformation($request);
        if (!$this->storageDisk->exists($fileInformation['path'])) {
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
        return $this->storageDisk->delete($path);
    }

    /**
     * Clear whole storage page cache
     *
     * @return bool
     */
    public function clear(): bool
    {
        return $this->storageDisk->delete($this->cacheDirectory);
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
        $pageName = $request->getMethod() . '.' . (!empty(basename($path)) ? basename($path) : self::INDEX_NAME);

        // Get file extension information
        [$fileExtension, $contentType] = $this->getFileExtension($headersBag);

        // Check if we should include query strings in file name
        if ($this->isQueryShouldCache) {
            if (!empty($request->all())) {
                $pageName .= '.' . urlencode(json_encode($request->all()));
            }
        }

        // Attach extension to file name
        $fileName = $pageName . $fileExtension;

        // File path
        $filePath = DIRECTORY_SEPARATOR;
        if ($pageName !== self::INDEX_NAME) {
            $filePath = $filePath . dirname($path);
        }

        return [
            'name' => $fileName,
            'extension' => $fileExtension,
            'directory' => $filePath,
            'mimeType' => $contentType,
            'path' => $filePath . DIRECTORY_SEPARATOR . $fileName,
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

        $contentTypeBag = explode(';', $headers->get('content-type'));
        $sourceContentType = array_shift($contentTypeBag);

        foreach (Config::get('bizmark.quicksilver::contentTypes', []) as $knownContentType => $extension) {
            if ($knownContentType === $sourceContentType) {
                return [$extension, $knownContentType];
            }
        }

        return ['.html', 'text/html'];
    }
}
