<?php namespace BizMark\Quicksilver\Classes\Caches;

use October\Rain\Argon\Argon;
use Storage, Config;
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
     * Default index file name
     */
    const INDEX_NAME = 'qs_index_qs';

    /**
     * Data folder inside disk
     */
    const DATA_FOLDER = 'data';

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
        $lastModified = Argon::parse($this->storageDisk->lastModified($fileInformation['path']))->toRfc7231String();
        return new Response($this->storageDisk->get($fileInformation['path']), 200, [
            'Content-Type' => $fileInformation['mimeType'],
            'Last-Modified' => $lastModified
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
        if (!$this->storageDisk->exists(self::DATA_FOLDER)) {
            $this->storageDisk->makeDirectory(self::DATA_FOLDER);
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
        if (!$this->storageDisk->delete(self::DATA_FOLDER . DIRECTORY_SEPARATOR . $path)) {
            $files = $this->storageDisk->files(self::DATA_FOLDER . DIRECTORY_SEPARATOR . dirname($path));
            if (!empty($files) && count($files) > 0) {
                $matchedFiles = preg_grep('*\.'.basename($path).'\.*', $files);
                if (!empty($matchedFiles) && count($matchedFiles) > 0) {
                    return $this->storageDisk->delete($matchedFiles);
                }
            }

            return $this->storageDisk->deleteDirectory(self::DATA_FOLDER . DIRECTORY_SEPARATOR . $path);
        }
    }

    /**
     * Clear whole storage page cache
     *
     * @return bool
     */
    public function clear(): bool
    {
        return $this->storageDisk->deleteDirectory(self::DATA_FOLDER);
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
            return ['html', 'text/html'];
        }

        $contentTypeBag = explode(';', $headers->get('content-type'));
        $sourceContentType = array_shift($contentTypeBag);

        foreach (Config::get('bizmark.quicksilver::contentTypes', []) as $knownContentType => $extension) {
            if ($knownContentType === $sourceContentType) {
                return [$extension, $knownContentType];
            }
        }

        return ['html', 'text/html'];
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
        $requestedPath = $request->path();
        $headersBag = !empty($response) ? $response : $request;
        [$fileExtension, $contentType] = $this->getFileExtension($headersBag);

        // Prepare file name as separate array elements
        $pageNameElements = [
            strtolower($request->getMethod()),
            (!empty(basename($requestedPath)) ? basename($requestedPath) : self::INDEX_NAME)
        ];

        // Check if we should include query strings in file name
        if ($this->isQueryShouldCache) {
            if (!empty($request->all())) {
                $pageNameElements[] = urlencode(json_encode($request->all()));
            }
        }

        // Put file extension information
        $pageNameElements[] = $fileExtension;

        // Glue page elements array in to string file name.
        $fileName = implode('.', $pageNameElements);

        // File directory
        $fileDirectory = self::DATA_FOLDER . DIRECTORY_SEPARATOR . dirname($requestedPath);

        return [
            'name' => $fileName,
            'extension' => $fileExtension,
            'directory' => $fileDirectory,
            'mimeType' => $contentType,
            'path' => $fileDirectory . DIRECTORY_SEPARATOR . $fileName,
        ];
    }
}
