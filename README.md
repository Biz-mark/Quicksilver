# OctoberCMS lightning fast static files cache system

This is adaptation of package: [JosephSilber/page-cache](https://github.com/JosephSilber/page-cache) (Go get him some stars)

Lighnintg fast cache system that converts your website page to static html files.
Super useful for anyone who wants their static website to be more fast.

## Installation

Open Settings in your control panel of your OctoberCMS website. Go to Updates & Plugins and in search bar type "Quicksilver". Install it by clicking on icon.

## Configuration

No configuration needed! :)

**Just be sure that plugin can create/write/read "page-cache" folder in your storage path.**

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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

Developed by Joseph Silber, adapted for OctoberCMS by Nick Khaetsky at Biz-Mark.