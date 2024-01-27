<?php

namespace Kindness\ModuleManage;

use DI\Container;
use Kindness\ModuleManage\Utils\MergeVendorPlugin;
use Webman\Bootstrap;
use Webman\Config;
use Webman\Middleware;
use Workerman\Worker;
use Dotenv\Dotenv;

class Module implements Bootstrap
{
    /**
     * The self instance.
     *
     * @var Module|null
     */
    public static $instance = null;

    /**
     * All $applications with scan market folder.
     *
     * @var array
     */
    public $applications = [];

    /**
     * The Worker instance.
     *
     * @var Worker|null
     */
    public $worker;

    /**
     * Start Container Server.
     *
     * @param Worker $worker
     * @return void
     */
    public static function start($worker)
    {
        require_once __DIR__ . '/helpers.php';

        $market = self::getInstance();
        $market->worker = $worker;
        $market->reload();
        $market->boot();
    }

    /**
     * Get current class instance.
     *
     * @return Module
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new Module();
        }
        return self::$instance;
    }

    /**
     * Reload module.json.
     *
     * @return void
     */
    public function reload()
    {
        $jsonPaths = glob(module_path() . '/**/module.json');
        foreach ($jsonPaths as $path) {
            $this->applications[] = json_decode(file_get_contents($path), true);
        }
    }

    /**
     * Start activity containers.
     *
     * @return void
     */
    public function boot()
    {
        $namespace = Config::get('plugin.kindness.module-manage.app.namespace', 'plugin');
        $activities = $this->getActivity();

        $mergeVendorManager = new MergeVendorPlugin();
        $mergeVendorManager->init();
        foreach ($activities as $activity) {
            $moduleName = $activity['name'];

            // 加载模块公共函数文件
            if (file_exists(module_path($moduleName, 'app/functions.php'))) {
                require module_path($moduleName, 'app/functions.php');
            }
            // 加载模块 composer 拓展包文件
            if (file_exists(module_path($moduleName, 'composer.json')) && is_dir(module_path($moduleName, 'vendor'))) {
                $mergeVendorManager->addVendor(module_path($moduleName, 'vendor'));
            }
            // 加载 env 环境配置文件
            if (class_exists('Dotenv\Dotenv') && file_exists(module_path($moduleName, '.env'))) {
                if (method_exists('Dotenv\Dotenv', 'createUnsafeImmutable')) {
                    Dotenv::createUnsafeImmutable(module_path($moduleName))->load();
                } else {
                    Dotenv::createMutable(module_path($moduleName))->load();
                }
            }
            // 启动模块服务容器
            $app = require_once module_path($moduleName) . '/bootstrap/app.php';
            if ($app instanceof \Illuminate\Contracts\Foundation\Application) {
                if (!is_null($this->worker) && $this->worker->name == 'plugin.kindness.module-manage.monitor') {
                    Worker::safeEcho("<n><g>[INFO]</g> 应用模块 {$moduleName} 已启动.</n>" . PHP_EOL);
                }
                $app->make('kernel')->handle();
                Middleware::load(
                    ["plugin.$moduleName" => [\Kindness\ModuleManage\Foundation\Support\Middlewares\RequestMiddleware::class]]
                );
            }
        }
    }

    /**
     * Get activity applications.
     *
     * @return array
     */
    public function getActivity()
    {
        return array_filter($this->applications, function ($item) {
            return $item['activity'];
        });
    }

    /**
     * Get applications.
     *
     * @return array
     */
    public function getApplications()
    {
        return $this->applications;
    }

    /**
     * Find the container of name.
     *
     * @param $name
     * @return array
     */
    public function find($name)
    {
        foreach ($this->applications as $application) {
            if ($application['name'] == $name) {
                return $application;
            }
        }
        return [];
    }
}
