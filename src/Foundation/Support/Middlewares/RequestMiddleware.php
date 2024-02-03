<?php

namespace Kindness\ModuleManage\Foundation\Support\Middlewares;

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
        return $handler($request);
    }
}
