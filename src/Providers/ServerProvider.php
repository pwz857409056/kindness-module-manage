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
     * @var ContainerInterface|\Slince\Di\Container
     */
    protected $container;

    /**
     * The Worker instance.
     *
     * @var Worker|null
     */
    protected $worker;

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
    }


    public function register()
    {

    }

    /**
     * Call the method when container started.
     *
     * @return void
     */
    abstract public function boot();
}
