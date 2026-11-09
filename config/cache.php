<?php
return ['default'=>env('CACHE_DRIVER','file'),'stores'=>['file'=>['driver'=>'file','path'=>storage_path('framework/cache/data')],'array'=>['driver'=>'array'],'database'=>['driver'=>'database','table'=>'cache'],'redis'=>['driver'=>'redis','connection'=>'cache']],'prefix'=>env('CACHE_PREFIX','mehfil_cache')];
