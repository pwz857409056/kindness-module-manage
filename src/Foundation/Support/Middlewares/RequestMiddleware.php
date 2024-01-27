<?php

namespace Kindness\ModuleManage\Foundation\Support\Middlewares;

use Kindness\ModuleManage\Exceptions\BusinessException;
use Kindness\ModuleManage\Module;
use Kindness\ModuleManage\Response\ResponseEnum;
use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class RequestMiddleware implements MiddlewareInterface
{
    public function process(Request $request, callable $handler): Response
    {
        if ($request->plugin) {
            app($request->plugin)['request'] = $request;
        }
        $response = $handler($request);
        return $response;
    }
}
