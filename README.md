# OctoberCMS Lightning-Fast Static File Cache System

[Quicksilver on the OctoberCMS Marketplace](https://octobercms.com/plugin/BizMark-quicksilver)

Quicksilver is a lightning-fast caching system that converts your website pages into static `.html`, `.xml`, `.json`, and other file formats.

By storing pages as static files, Quicksilver delivers content to your visitors in milliseconds.

---

## Features

- No additional configuration required — easy to use even on shared hosting. Install with one click.
- Intelligent content type detection based on response headers or file extensions, with extensibility support.
- Full support for Laravel’s Storage service, including custom cache disks.
- Optional query string caching, configurable from the backend.
- Optional excluded paths, configurable from the backend.
- Easily extendable via October CMS events.
- No external dependencies; compatible with October CMS v1.1, v2, and v3.
- Can be used together with the October CMS AJAX framework.

---

## Important Notice

> This plugin stores page responses as static files. Cached pages are **identical for all visitors**, regardless of whether a user is logged in or not.  
> As a result, sensitive or personalized information may be exposed if such pages are cached.

Use the Quicksilver settings to define excluded paths so that pages containing sensitive data are not cached.

---

## Requirements

- PHP 7.4 or higher
- October CMS v1.1, v2, or v3

---

## Installation
```bash
php artisan plugin:install BizMark.Quicksilver
```

---

## Additional configuration

Quicksilver can be configured so that the web server serves cached pages directly, completely bypassing application bootstrapping.

### Apache

1. Open your `.htaccess` file and add the following rules before the `Standard routes` section:

    ```apacheconfig
    ##
    ## Serve Cached Page If Available
    ##
    RewriteCond %{REQUEST_URI} ^/?$
    RewriteCond %{DOCUMENT_ROOT}/storage/quicksilver/cache/qs_index_qs.html -f
    RewriteRule .? /storage/quicksilver/cache/qs_index_qs.html [L]
    RewriteCond %{DOCUMENT_ROOT}/storage/quicksilver/cache%{REQUEST_URI}.html -f
    RewriteRule . /storage/quicksilver/cache%{REQUEST_URI}.html [L]
    RewriteCond %{HTTP:X-Requested-With} XMLHttpRequest
    RewriteRule !^index.php index.php [L,NC]
    ```

2. Comment out the following line in the `White listed folders` section:
    ```
    RewriteRule !^index.php index.php [L,NC]
    ```

3. **Ensure the plugin has read/write permissions for the `storage/quicksilver/cache` directory.**

### Nginx

```nginx
location = / {
    try_files /storage/quicksilver/cache/qs_index_qs.html /index.php?$query_string;
}

location / {
    try_files $uri $uri/ /storage/quicksilver/cache/$uri.html /storage/quicksilver/cache/$uri.json /index.php?$query_string;
}
```

If you need to send AJAX requests to cached URLs, use the following configuration:

```nginx
location / {
    if ($request_method = POST ) {
        rewrite ^/.*$ /index.php last;
    }

    try_files $uri $uri/ /storage/quicksilver/cache/$uri.html /storage/quicksilver/cache/$uri.json /index.php?$query_string;
}
```

---

### Ignoring the cached files

Do not forget to exclude the Quicksilver cache directory from version control by adding it to your `.gitignore` file:

```
/storage/quicksilver
```

---

## Clearing the cache


Clear the entire Quicksilver cache:

```
php artisan quicksilver:clear
```

Clear the cache for a specific path:
```
php artisan quicksilver:clear {path}
```

---

## Events

These events are triggered during request and response validation.
If an event listener returns `false`, caching will be aborted.

- `bizmark.quicksilver.is_request_valid` - bool

```php

Event::listen('bizmark.quicksilver.is_request_valid', function(\Illuminate\Http\Request $request) {
    // request is valid, cache.
    return true;
    
    // request is invalid, don't cache.
    return false;
});

```

- `bizmark.quicksilver.is_response_valid` - bool

```php

Event::listen('bizmark.quicksilver.is_response_valid', function(\Symfony\Component\HttpFoundation\Response; $response) {
    // response is valid, cache.
    return true;
    
    // response is invalid, don't cache.
    return false;
});

```

These events are triggered before and after a cached page is stored, allowing you to modify its contents.

- `bizmark.quicksilver.before_store` - void
```php
Event::listen('bizmark.quicksilver.before_store', function(array $fileInformation) {
    // Contents of fileInformation
    // $fileInformation = [
    //     name
    //     extension
    //     directory
    //     mimeType
    //     path
    // ];
    // ...
});
```

- `bizmark.quicksilver.after_store` - void
```php
Event::listen('bizmark.quicksilver.after_store', function(array $fileInformation) {
    // Contents of fileInformation
    // $fileInformation = [
    //     name
    //     extension
    //     directory
    //     mimeType
    //     path
    // ];
    // ...
});
```

---

© 2026, Nick Khaetsky at [Biz-Mark](https://biz-mark.ru/) under the [GNU General Public License v2.0](https://choosealicense.com/licenses/gpl-2.0/).
