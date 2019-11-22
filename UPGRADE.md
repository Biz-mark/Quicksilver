# Upgrade guide
   
   - [Upgrading to 1.4 from 1.x](#upgrade-1.4)
   
   <a name="upgrade-1.4"></a>
   ## Upgrading To 1.4
   
   if you have 1.3 version, just delete this two rows in .htaccess of your project
   
   ```apacheconfig
   RewriteCond %{QUERY_STRING} ^(.)
   RewriteRule !^index.php index.php [L,NC]
   ```
