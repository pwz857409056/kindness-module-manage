<?php

namespace Kindness\ModuleManage\Providers;

use Psr\Container\ContainerInterface;
use support\Container;
use Workerman\Worker;

abstract class ServerProvider
{
    /**
     * The application instance.
     *
     * @var ContainerInterface|\DI\Container
     */
    protected $container;

    /**
     * The Worker instance.
     *
     * @var Worker|null
     */
    protected $worker;

    protected $plugin;

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
        $this->plugin = $plugin;
        $this->init();
    }

    private function init()
    {
        if (config("plugin.$this->plugin.dependence") !== null) {
            foreach (config("plugin.$this->plugin.dependence") as $abstract => $concrete) {
                $this->container->set($abstract, $concrete);
            }
        }
    }

    /**
     * Call the method when container started.
     *
     * @return void
     */
    abstract public function boot();
}
