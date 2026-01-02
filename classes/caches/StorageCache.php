<?php namespace BizMark\Quicksilver\Classes\Caches;

use Storage, Config, Event;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use October\Rain\Support\Date;
use BizMark\Quicksilver\Models\Settings;
use BizMark\Quicksilver\Classes\Contracts\Quicksilver;

/**
 * StorageCache class.
 *
 * Handles filesystem-based page caching.
 *
 * @package BizMark\Quicksilver\Classes\Caches
 * @author Nick Khaetsky, Biz-Mark
 */
class StorageCache extends AbstractCache
{
    /**
     * Event name triggered before a file is stored in cache.
     */
    const EVENT_BEFORE_STORE = 'bizmark.quicksilver.before_store';

    /**
     * Event name triggered after a file is stored in cache.
     */
    const EVENT_AFTER_STORE = 'bizmark.quicksilver.after_store';

    /**
     * Default index file name.
     */
    const INDEX_NAME = 'qs_index_qs';

    /**
     * Cache data directory inside the storage disk.
     */
    const DATA_FOLDER = 'cache';

    /**
     * Indicates whether pages with query strings should be cached.
     *
     * @var bool
     */
    private $isQueryShouldCache;

    /**
     * Supported content types mapped to file extensions.
     *
     * @var array
     */
    protected $contentTypes;

    /**
     * Default response headers.
     *
     * @var array
     */
    protected $defaultHeaders;

    /**
     * Quicksilver storage disk instance.
     *
     * @var \Illuminate\Contracts\Filesystem\Filesystem|Storage
     */
    private $storageDisk;

    /**
     * Default file extension and MIME type.
     *
     * @var array
     */
    private $defaultFileExtension = ['html', 'text/html'];

    /**
     * StorageCache constructor.
     */
    public function __construct()
    {
        $this->isQueryShouldCache = Settings::get('cache_query_strings', false);
        $this->contentTypes = Config::get('bizmark.quicksilver::contentTypes', []);
        $this->storageDisk = Storage::disk(Config::get('bizmark.quicksilver::default'));
        $this->defaultHeaders = array_filter(
            Config::get('bizmark.quicksilver::defaultHeaders', []),
            function ($value) {
                return !empty($value);
            }
        );
    }

    /**
     * Retrieve a cached response from storage.
     *
     * @param Request $request
     * @return Response
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function get(Request $request): Response
    {
        $fileInformation = $this->getFileInformation($request);
        $lastModified = Date::parse(
            $this->storageDisk->lastModified($fileInformation['path'])
        )->toRfc7231String();

        return new Response(
            $this->storageDisk->get($fileInformation['path']),
            200,
            array_merge($this->defaultHeaders, [
                'Content-Type'  => $fileInformation['mimeType'],
                'Last-Modified' => $lastModified,
            ])
        );
    }

    /**
     * Store a response in the cache storage.
     *
     * @param Request  $request
     * @param Response $response
     * @return Quicksilver
     */
    public function store(Request $request, Response $response): Quicksilver
    {
        if (!$this->storageDisk->exists(self::DATA_FOLDER)) {
            $this->storageDisk->makeDirectory(self::DATA_FOLDER);
        }

        $fileInformation = $this->getFileInformation($request, $response);

        Event::fire(self::EVENT_BEFORE_STORE, [$fileInformation]);

        // Ensure the target directory exists
        if (!$this->storageDisk->exists($fileInformation['directory'])) {
            $this->storageDisk->makeDirectory($fileInformation['directory']);
        }

        // Store the response content in the cache
        $this->storageDisk->put(
            $fileInformation['path'],
            $response->getContent()
        );

        Event::fire(self::EVENT_AFTER_STORE, [$fileInformation]);

        return $this;
    }

    /**
     * Determine whether a cached version of the request exists.
     *
     * @param Request $request
     * @return bool
     */
    public function has(Request $request): bool
    {
        if (!$this->isQueryShouldCache && !empty($request->all())) {
            return false;
        }

        $fileInformation = $this->getFileInformation($request);

        return $this->storageDisk->exists($fileInformation['path']);
    }

    /**
     * Remove a specific path from the cache storage.
     *
     * @param string $path
     * @return bool
     */
    public function forget(string $path): bool
    {
        // If the path ends with a slash, remove the entire directory
        if (substr($path, -1) === '/') {
            return $this->storageDisk->deleteDirectory(
                self::DATA_FOLDER . DIRECTORY_SEPARATOR . $path
            );
        }

        // Otherwise, search for matching cached files
        $files = $this->storageDisk->files(
            self::DATA_FOLDER . DIRECTORY_SEPARATOR . dirname($path)
        );

        if (!empty($files)) {
            $matchedFiles = preg_grep('*\.' . basename($path) . '\.*', $files);
            if (!empty($matchedFiles)) {
                return $this->storageDisk->delete($matchedFiles);
            }
        }

        return true;
    }

    /**
     * Clear the entire storage cache.
     *
     * @return bool
     */
    public function clear(): bool
    {
        return $this->storageDisk->deleteDirectory(self::DATA_FOLDER);
    }

    /**
     * Build file metadata for the requested resource.
     *
     * @param Request       $request
     * @param Response|null $response
     * @return array
     */
    protected function getFileInformation(Request $request, Response $response = null): array
    {
        $requestedPath = $request->path();
        [$fileExtension, $contentType] = $this->determineFileExtension($request, $response);

        // Prepare filename components
        $pageNameElements = [
            strtolower($request->getMethod()), // get, post, etc.
            $this->determineFileName($requestedPath),
        ];

        // Append query string data if enabled
        if ($this->isQueryShouldCache && !empty($request->all())) {
            $pageNameElements[] = urlencode(json_encode($request->all()));
        }

        // Append file extension
        $pageNameElements[] = $fileExtension;

        // Build final filename
        $fileName = implode('.', $pageNameElements);

        // Resolve directory path
        $fileDirectory = self::DATA_FOLDER . DIRECTORY_SEPARATOR . dirname($requestedPath);

        return [
            'name'      => $fileName,
            'extension' => $fileExtension,
            'directory' => $fileDirectory,
            'mimeType'  => $contentType,
            'path'      => $fileDirectory . DIRECTORY_SEPARATOR . $fileName,
        ];
    }

    /**
     * Determine the base file name from the requested path.
     *
     * @param string $requestedPath
     * @return string
     */
    protected function determineFileName(string $requestedPath): string
    {
        $path = basename($requestedPath);

        if (empty($path)) {
            return self::INDEX_NAME;
        }

        // Strip known file extensions from the path
        foreach ($this->contentTypes as $extension) {
            $extension = '.' . $extension;
            if (substr($path, -strlen($extension)) === $extension) {
                return str_replace($extension, '', $path);
            }
        }

        return $path;
    }

    /**
     * Determine file extension and MIME type from headers or request path.
     *
     * @param Request       $request
     * @param Response|null $response
     * @return array
     */
    protected function determineFileExtension(Request $request, Response $response = null): array
    {
        $path = basename($request->path());
        $headers = $response ? $response->headers : $request->headers;

        if (!$headers || !$headers->has('content-type')) {
            return $this->defaultFileExtension;
        }

        $contentTypeBag = explode(';', $headers->get('content-type'));
        $headerContentType = array_shift($contentTypeBag);

        // Match header content type with supported MIME types
        foreach ($this->contentTypes as $contentType => $extension) {
            if ($contentType === $headerContentType) {
                return [$extension, $contentType];
            }
        }

        // Fallback: detect extension from request path
        if (!empty($path)) {
            foreach ($this->contentTypes as $contentType => $extension) {
                $extensionWithDot = '.' . $extension;
                if (substr($path, -strlen($extensionWithDot)) === $extensionWithDot) {
                    return [$extension, $contentType];
                }
            }
        }

        return $this->defaultFileExtension;
    }
}
