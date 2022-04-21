# Upgrade guide

- [Upgrading to 2.1.1 from 3.0](#upgrade-3.0)
- [Upgrading to 1.3 from 1.2.2](#upgrade-1.3)

<a name="upgrade-3.0"></a>

## Upgrading To 3.0

1. Cache folder changed from `/storage/page-cache` to `/storage/quicksilver/cache`
2. Webserver configuration (.htaccess or nginx) is optional now, and can be removed. Quicksilver has more features without webserver configuration.


<a name="upgrade-1.3"></a>

## Upgrading To 1.3

Open your .htaccess file, and replace old section of "serving cache rules" with new from documentation

If you edited this section, or don't want to replace whole section, you need to add this two lines right after section headline.

```apacheconfig
RewriteCond %{QUERY_STRING} ^(.)
RewriteRule !^index.php index.php [L,NC]
```
