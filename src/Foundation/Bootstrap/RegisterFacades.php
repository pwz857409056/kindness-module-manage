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
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @return void
     */
    public function bootstrap(Application $app)
    {
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication($app);
        AliasLoader::getInstance(Facade::defaultAliases()->toArray())->register();
    }
}
