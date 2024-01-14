<?php

use Kindness\ModuleManage\Commands\ModuleCreateCommand;
use Kindness\ModuleManage\Commands\ModuleListCommand;
use Kindness\ModuleManage\Commands\ModuleMakeController;
use Kindness\ModuleManage\Commands\ModuleMakeService;
use Kindness\ModuleManage\Commands\ModuleMakeRequest;
use Kindness\ModuleManage\Commands\ModuleMakeMiddleware;
use Kindness\ModuleManage\Commands\ModuleStart;
use Kindness\ModuleManage\Commands\ModuleStop;

return [
    ModuleListCommand::class,
    ModuleCreateCommand::class,
    ModuleMakeController::class,
    ModuleMakeService::class,
    ModuleMakeRequest::class,
    ModuleMakeMiddleware::class,
    ModuleStart::class,
    ModuleStop::class,
];
