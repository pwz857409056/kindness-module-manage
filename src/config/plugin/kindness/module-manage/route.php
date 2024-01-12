<?php

/*
|--------------------------------------------------------------------------
| Container Routes
|--------------------------------------------------------------------------
*/

use Kindness\ModuleManage\Module;
use Webman\Route;

$activityApplications = Module::getInstance()->getActivity();
// 注册已启用的模块应用路由配置
foreach ($activityApplications as $activityApplication) {
    if (empty($activityApplication['name'])) continue;
    $paths = glob(module_path($activityApplication['name']) . '/routes/*.php');
    if (isset($activityApplication['route']['urlPrefix']) && !empty($activityApplication['route']['urlPrefix'])) {
        foreach ($paths as $path) {
            Route::group($activityApplication['route']['urlPrefix'], function () use ($path) {
                require_once $path;
            });
        }
    } else {
        foreach ($paths as $path) {
            require_once $path;
        }
    }
}
Route::any('[{path:.+}]', function () {
    $module = Module::getInstance()->find(request()->plugin);
    if ($module && $module['activity']) {
        return json([
            'status' => 'fail',
            'code' => '404',
            'message' => 'no found api',
            'data' => [],
            'error' => [],
        ]);
    } else {
        return 404;
    }
});

