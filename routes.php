<?php

use Silber\PageCache\Cache;

Route::get('/hey', function (){
    $cache = new Cache();
   return 'hey';
});