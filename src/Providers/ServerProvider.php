<?php

namespace Kindness\ModuleManage\Providers;

use Kindness\ModuleManage\Foundation\Application;
use Psr\Container\ContainerInterface;
use support\Container;
use Workerman\Worker;

abstract class ServerProvider
{
    /**
     * The application instance.
     *
     * @var ContainerInterface|Application|\Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * The Worker instance.
     *
     * @var Worker|null
     */
    protected $worker;

    protected array $bootstrappers = [
        \Kindness\ModuleManage\Foundation\Bootstrap\RegisterFacades::class,
        \Kindness\ModuleManage\Foundation\Bootstrap\RegisterProviders::class,
        \Kindness\ModuleManage\Foundation\Bootstrap\LoadConfiguration::class,
    ];

    /**
     * Create a new service provider instance.
     *
     * @param Worker|null $worker
     * @return void
     */
    public function __construct($worker, string $plugin = '')
    {
        $this->container = Container::instance($plugin);
        $this->worker = $worker;
        $this->init($plugin);
    }

    private function init($plugin = '')
    {
        if (config("plugin.$plugin.dependence") !== null) {
            foreach (config("plugin.$plugin.dependence") as $abstract => $concrete) {
                $this->container->set($abstract, $concrete);
            }
        }
        $this->container->bootstrapWith($this->bootstrappers);
    }

    /**
     * Call the method when container started.
     *
     * @return void
     */
    abstract public function boot();
}
