<?php

namespace Kindness\ModuleManage\Foundation\Bootstrap;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Config\Repository as RepositoryContract;
use SplFileInfo;
use Webman\Config;

class LoadConfiguration
{
    /**
     * Bootstrap the given application.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @return void
     */
    public function bootstrap(Application $app)
    {
        $app->instance('config', $config = new Config());
        // Finally, we will set the application's environment based on the configuration
        // values that were loaded. We will pass a callback which will be used to get
        // the environment in a web context where an "--env" switch is not present.
        $app->detectEnvironment(fn() => $config->get('app.env', 'production'));
    }
}
