<?php
return [
    'enable' => true,

    /*
     |--------------------------------------------------------------------------
     | The module namespace
     | 模块的命名空间
     |--------------------------------------------------------------------------
     |
     | This is consistent with the webman plugin set, or can be set separately. e.g. module
     | 这里和官方的 plugin 保持一致，也可以单独设置，例如 module
     |
     */
    'namespace' => 'plugin',

    'paths' => [
        /*
        |--------------------------------------------------------------------------
        | Module folder Path
        | 模块应用文件夹路径
        |--------------------------------------------------------------------------
        |
        | This path used for save the generated Container.
        | 模块应用生成路径
        |
        */
        'module' => base_path() . DIRECTORY_SEPARATOR . 'plugin',

        /*
         |--------------------------------------------------------------------------
         | Stub Path
         | Stub 模板文件路径
         |--------------------------------------------------------------------------
         |
         | Customize path location.
         | 自定义模板的路径，你可以修改默认模板
         |
         */
        'stub_path' => base_path() . DIRECTORY_SEPARATOR . 'vendor/kindness/module-manage/stubs',

        /*
         |--------------------------------------------------------------------------
         | Module application folder generation path
         | 模块应用文件夹生成路径
         |--------------------------------------------------------------------------
         |
         | Customize the initialization directory structure.
         | 自定义的初始化文件目录
         |
         */
        'generator' => [
            'controller' => 'app/controllers',
            'model' => 'app/models',
            'middleware' => 'app/middleware',
            'config' => 'config',
            'bootstrap' => 'bootstrap',
            'route' => 'routes',
            'service' => 'app/services',
            'request' => 'app/requests',
        ],

        /*
         |--------------------------------------------------------------------------
         | Stub Path
         | 模板路径
         |--------------------------------------------------------------------------
         |
         | Customize the properties of the makefile.
         | 自定义模板的路径和对应关系
         |
         */
        'stub' => [
            'moduleJson' => ['from' => 'module.stub', 'to' => '/module.json'],
            'app-kernel' => ['from' => 'app/kernel.stub', 'to' => '/app/kernel.php'],
            'bootstrap-app' => ['from' => 'bootstrap/app.stub', 'to' => '/bootstrap/app.php'],
            'config-app' => ['from' => 'config/app.stub', 'to' => '/config/app.php'],
            'config-autoload' => ['from' => 'config/autoload.stub', 'to' => '/config/autoload.php'],
            'config-container' => ['from' => 'config/container.stub', 'to' => '/config/container.php'],
            'config-dependence' => ['from' => 'config/dependence.stub', 'to' => '/config/dependence.php'],
            'config-database' => ['from' => 'config/database.stub', 'to' => '/config/database.php'],
            'config-exception' => ['from' => 'config/exception.stub', 'to' => '/config/exception.php'],
            'config-jwt' => ['from' => 'config/jwt.stub', 'to' => '/config/jwt.php'],
            'config-log' => ['from' => 'config/log.stub', 'to' => '/config/log.php'],
            'config-middleware' => ['from' => 'config/middleware.stub', 'to' => '/config/middleware.php'],
            'config-process' => ['from' => 'config/process.stub', 'to' => '/config/process.php'],
            'config-route' => ['from' => 'config/route.stub', 'to' => '/config/route.php'],
            'config-static' => ['from' => 'config/static.stub', 'to' => '/config/static.php'],
            'config-translation' => ['from' => 'config/translation.stub', 'to' => '/config/translation.php'],
            'controller-BaseController' => ['from' => 'controller/BaseController.stub', 'to' => '/app/controllers/BaseController.php'],
            'service-BaseService' => ['from' => 'service/BaseService.stub', 'to' => '/app/services/BaseService.php'],
            'provider' => ['from' => 'providers/AppServiceProvider.stub', 'to' => '/app/providers/AppServiceProvider.php'],
            'response' => ['from' => 'response/ResponseEnum.stub', 'to' => '/response/ResponseEnum.php'],
            'requests-BaseValidate' => ['from' => 'requests/BaseValidate.stub', 'to' => '/app/requests/BaseValidate.php'],
            'model-BaseModel' => ['from' => 'model/BaseModel.stub', 'to' => '/app/models/BaseModel.php'],
            'app-middleware-EnableCrossRequest' => ['from' => 'middleware/EnableCrossRequest.stub', 'to' => '/app/middleware/EnableCrossRequest.php'],
            'functions' => ['from' => 'functions.stub', 'to' => '/app/functions.php'],
            'route-api' => ['from' => 'route/api.stub', 'to' => '/routes/api.php'],
            'LICENSE' => ['from' => 'LICENSE.stub', 'to' => '/LICENSE.md'],
            'README' => ['from' => 'README.stub', 'to' => '/README.md'],
            'gitignore' => ['from' => 'gitignore.stub', 'to' => '/.gitignore'],
            'composerJson' => ['from' => 'composer.stub', 'to' => '/composer.json'],
        ]
    ]
];
