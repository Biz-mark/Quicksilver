# OctoberCMS lightning fast static files cache system

[Quicksilver in OctoberCMS Marketplace](https://octobercms.com/plugin/BizMark-quicksilver)

Lightning fast cache system that converts your website page to static .html, .xml, .json and other files.

Store your pages as static files and deliver it to your visitors in milliseconds!

## Features

- No additional configuration needed! Easy to use on shared hosting! Install in one click.
- Smart content type determination by headers or file extension, with ability to be extended. 
- Full Storage service support. You can configure custom cache disk via config.
- Optional query strings support configurable from backend.
- Optional excluded paths configurable from backend.
- Easy extendability by October CMS events.
- No dependencies, works with October CMS v1.1, v2, v3.
- Can be used with October CMS AJAX Framework!

## Notice

> This plugin stores the response of your pages as static files. You should be aware that cached pages are **the same to everyone** no matter in what state (logged or not) visitor session are. Otherwise, personal information can be seen by every visitor.

Use Quicksilver settings to set excluded paths, so pages with sensitive information will not be cached.

---

## Requirements

- PHP 7.4 and above
- October CMS v1.1 or v2 or v3

## Installation

```bash
php artisan plugin:install BizMark.Quicksilver
```

## Additional configuration

Quicksilver can be configured additionally so webserver can check for cached pages by itself, 
completely ignoring application booting.

### Apache

1. Open `.htaccess` and add the following before `Standard routes` section

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

2. Comment out following line in `White listed folders` section.
    ```
    RewriteRule !^index.php index.php [L,NC]
    ```

3. **Be sure that plugin can create/write/read "storage/quicksilver/cache" folder in your storage path.**

### Nginx

```nginx
location = / {
    try_files /storage/quicksilver/cache/qs_index_qs.html /index.php?$query_string;
}

location / {
    try_files $uri $uri/ /storage/quicksilver/cache/$uri.html /storage/quicksilver/cache/$uri.json /index.php?$query_string;
}
```

If you need to send ajax requests to cached url, you should use this construction

```nginx
location / {
    if ($request_method = POST ) {
        rewrite ^/.*$ /index.php last;
    }

    try_files $uri $uri/ /storage/quicksilver/cache/$uri.html /storage/quicksilver/cache/$uri.json /index.php?$query_string;
}
```


### Ignoring the cached files

Don't forget to put Quicksilver folder in your `.gitignore`.

```
/storage/quicksilver
```

## Clearing the cache



```
php artisan quicksilver:clear
```

Or to clear specific route
```
php artisan quicksilver:clear {path}
```

## Events

These events called when request and response are validated if returned true, Quicksilver will continue validation.

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

These events called before and after storing cached page, so you can modify contents of it.

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
© 2022, Nick Khaetsky at [Biz-Mark](https://biz-mark.ru/) under the [GNU General Public License v2.0](https://choosealicense.com/licenses/gpl-2.0/).
