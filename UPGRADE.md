# Upgrade guide

- [Upgrading to 1.3 from 1.2.2](#upgrade-1.3)

<a name="upgrade-1.3"></a>

## Upgrading To 1.3

Open your .htaccess file, and replace old section of "serving cache rules" with new from documentation

If you edited this section, or don't want to replace whole section, you need to add this two lines right after section headline.

```apacheconfig
RewriteCond %{QUERY_STRING} ^(.)
RewriteRule !^index.php index.php [L,NC]
```

## Upgrading to 2.0
If you used the ```BizMark\Quicksilver\Classes\Cache``` object directly, you must replace its use with an interface call via di. ```App::make(BizMark\Quicksilver\Classes\Contracts\Cache::class);```

If you used the plugin as standard without changes, no changes are required!
