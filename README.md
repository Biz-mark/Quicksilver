# OctoberCMS lightning fast static files cache system

[Quicksilver in OctoberCMS Marketplace](https://octobercms.com/plugin/bizmark-quicksilver)

This is adaptation of package: [JosephSilber/page-cache](https://github.com/JosephSilber/page-cache) (Go get him some stars)

Lightning fast cache system that converts your website page to static html files.
Super useful for anyone who wants their static website to be faster.

**Work in progress.**

### Notes

THIS CACHE SYSTEM ONLY SUITABLE FOR BASIC WEBSITES. BECAUSE OF STATIC FILES, YOU MAY SEE CHANGES ON WEBSITE ONLY AFTER CLEARING CACHE.

**Work still in progress.**

This plugin caches every route that opens by GET parameter with 200 response code. Except for every url that is matching your `'backendUri'` defined in `config/cms.php`.

## Installation

Open Settings in the control panel of your OctoberCMS website. Go to Updates & Plugins and in search bar type "Quicksilver". Install it by clicking on the icon.

## Configuration

1. Open `.htaccess` and add the following before `Standard routes` section

    ```apacheconfig
    ##
    ## Serve Cached Page If Available
    ##
    RewriteCond %{REQUEST_URI} ^/?$
    RewriteCond %{DOCUMENT_ROOT}/storage/page-cache/pc__index__pc.html -f
    RewriteRule .? /storage/page-cache/pc__index__pc.html [L]
    RewriteCond %{DOCUMENT_ROOT}/storage/page-cache%{REQUEST_URI}.html -f
    RewriteRule . /storage/page-cache%{REQUEST_URI}.html [L]
    RewriteCond %{HTTP:X-Requested-With} XMLHttpRequest
    RewriteRule !^index.php index.php [L,NC]
    ```

2. Comment out following line in `White listed folders` section.
    ```
    RewriteRule !^index.php index.php [L,NC]
    ``` 

3. **Be sure that plugin can create/write/read "page-cache" folder in your storage path.**

### Ignoring the cached files

To make sure you don't commit your locally cached files to your git repository, add this line to your `.gitignore` file:

```
/storage/page-cache
```

## Clearing the cache

Since the responses are cached to disk as static files, any updates to those pages in your app will not be reflected on your site. To update pages on your site, you should clear the cache with the following command:

```
php artisan page-cache:clear
```

As a rule of thumb, it's good practice to add this to your deployment script. That way, whenever you push an update to your site the page cache will automatically be cleared.

If you're using [Forge](https://forge.laravel.com)'s Quick Deploy feature, you should add this line to the end of your Deploy Script. This'll ensure that the cache is cleared whenever you push an update to your site.

You may optionally pass a URL slug to the command, to only delete the cache for a specific page:

```
php artisan page-cache:clear {slug}
```

## Customizing what to cache

By default, all GET requests with a 200 HTTP response code are cached. If you want to change that, create your own middleware that extends the package's base middleware, and override the `shouldCache` method with your own logic.

Example:
```php
<?php namespace Acme\Plugin\Middleware;

use Request;
use Response;

use BizMark\Quicksilver\Classes\Middleware\CacheResponse as BaseCacheResponse;

class CacheResponse extends BaseCacheResponse
{
    protected function shouldCache(Request $request, Response $response)
    {
        // In this example, we don't ever want to cache pages if the
        // URL contains a query string. So we first check for it,
        // then defer back up to the parent's default checks.
        if ($request->getQueryString()) {
            return false;
        }

        return parent::shouldCache($request, $response);
    }
}
```

Update the `Plugin.php` of `BizMark\Quicksilver` and pass your new `CacheResponse` class to `pushMiddleware()` method.

Don't forget to freeze all updates of Quicksilver plugin at settings of your OctoberCMS website. Otherwise all your changes in `Plugin.php` file will be overwritten by next update from marketplace.

---
© 2019, [Biz-Mark](https://biz-mark.ru/) under the [MIT license](https://opensource.org/licenses/MIT).

Adaptation for OctoberCMS by Nick Khaetsky at Biz-Mark.