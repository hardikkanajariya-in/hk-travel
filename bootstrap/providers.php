<?php

use App\Providers\AppServiceProvider;
use App\Providers\FortifyServiceProvider;
use App\Providers\HkCoreServiceProvider;
use App\Providers\ModuleServiceProvider;

return [
    AppServiceProvider::class,
    FortifyServiceProvider::class,
    HkCoreServiceProvider::class,
    ModuleServiceProvider::class,
];
