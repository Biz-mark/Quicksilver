# Upgrade Guide

- [Upgrading to 3.0 from earlier versions](#upgrade-3-0)
- [Upgrading to 1.3 from 1.2.2](#upgrade-1-3)

<a name="upgrade-3-0"></a>

## Upgrading to 3.0

1. The cache directory has changed from `/storage/page-cache` to `/storage/quicksilver/cache`.
2. Web server configuration (`.htaccess` or Nginx) is now optional and can be safely removed.
   Quicksilver provides additional features when running without direct web server rules.

---

<a name="upgrade-1-3"></a>

## Upgrading to 1.3

Open your `.htaccess` file and replace the old **cache serving rules** section with the updated version from the documentation.

If you have customized this section or prefer not to replace it entirely, add the following two lines **immediately after the section header**:

```apache
RewriteCond %{QUERY_STRING} ^(.)
RewriteRule !^index.php index.php [L,NC]
```
