<?php

namespace Kindness\ModuleManage\Foundation\Support\Middlewares;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class RequestMiddleware implements MiddlewareInterface
{
    /**
     * @desc: 这个主要是为了解决laravel的数据库包操作分页时的问题
     *
     * @param Request $request
     * @param callable $handler
     * @return Response
     */
    public function process(Request $request, callable $handler): Response
    {
        if ($request->plugin) {
            app($request->plugin)['request'] = $request;
        }
        return $handler($request);
    }
}
