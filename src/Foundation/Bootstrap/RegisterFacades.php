<?php

namespace Kindness\ModuleManage\Foundation\Bootstrap;

use Illuminate\Contracts\Foundation\Application;
use Kindness\ModuleManage\Foundation\AliasLoader;
use Illuminate\Support\Facades\Facade;

class RegisterFacades
{
    /**
     * Bootstrap the given application.
     *
     * @param Application $app
     * @return void
     */
    public function bootstrap(Application $app): void
    {
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication($app);
        AliasLoader::getInstance(Facade::defaultAliases()->toArray())->register();
    }
}
