<?php
use Kindness\ModuleManage\Commands\ModuleCreateCommand;
use Kindness\ModuleManage\Commands\ModuleListCommand;
use Kindness\ModuleManage\Commands\ModuleMakeController;
use Kindness\ModuleManage\Commands\ModuleMakeMiddleware;
use Kindness\ModuleManage\Commands\ModuleMakeModel;
use Kindness\ModuleManage\Commands\ModuleStart;
use Kindness\ModuleManage\Commands\ModuleStop;

return [
    ModuleListCommand::class,
    ModuleCreateCommand::class,
    ModuleMakeController::class,
    ModuleMakeModel::class,
    ModuleMakeMiddleware::class,
    ModuleStart::class,
    ModuleStop::class,
];
