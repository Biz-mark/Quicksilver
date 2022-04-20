# OctoberCMS lightning fast static files cache system

[Quicksilver in OctoberCMS Marketplace](https://octobercms.com/plugin/BizMark-quicksilver)

Lightning fast cache system that converts your website page to static .html, .xml, .json and other files.

## Installation

```bash
php artisan plugin:install BizMark.Quicksilver
```

## Configuration
### Apache

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

### Nginx

```nginx
location = / {
    try_files /storage/page-cache/pc__index__pc.html /index.php?$query_string;
}

location / {
    try_files $uri $uri/ /storage/page-cache/$uri.html /storage/page-cache/$uri.json /index.php?$query_string;
}
```

If you need to send ajax requests to cached url, you should use this construction

```nginx
location / {
    if ($request_method = POST ) {
        rewrite ^/.*$ /index.php last;
    }

    try_files $uri $uri/ /storage/page-cache/$uri.html /storage/page-cache/$uri.json /index.php?$query_string;
}
```


### Ignoring the cached files

To make sure you don't commit your locally cached files to your git repository, add this line to your `.gitignore` file:

```
/storage/page-cache
```

## Clearing the cache

Since the responses are cached to disk as static files, any updates to those pages in your app will not be reflected on your site. To update pages on your site, you should clear the cache with the following command:

```
php artisan quicksilver:clear
```

Or to clear specific route
```
php artisan quicksilver:clear {slug}
```

## Customizing what to cache


---
© 2022, [Biz-Mark](https://biz-mark.ru/) under the [MIT license](https://opensource.org/licenses/MIT).
