<?php

namespace Kindness\ModuleManage\Exceptions;

use Kindness\ModuleManage\Auth\Jwt\Exception\JwtException;
use Kindness\ModuleManage\Response\ApiResponse;
use think\exception\ValidateException;
use Throwable;
use Webman\Exception\ExceptionHandler;
use Webman\Http\Request;
use Webman\Http\Response;

class Handler extends ExceptionHandler
{
    use ApiResponse;

    public $dontReport = [

    ];

    public function render(Request $request, Throwable $exception): Response
    {
        $key = implode('.', ['plugin', request()->plugin, 'app']);
        // 自定义错误异常抛出
        if ($exception instanceof BusinessException) {
            return $this->fail([$exception->getCode(), $exception->getMessage()]);
        }
        if ($exception instanceof ValidateException) {
            return $this->fail([$exception->getCode(), $exception->getMessage()]);
        }
        if ($exception instanceof JwtException) {
            return $this->fail([$exception->getCode(), $exception->getMessage()]);
        }
        if (config("$key.debug")) {
            $data = [
                'exception' => $this->renderException($exception),
            ];
            $message = $exception->getMessage();
        } else {
            $data = null;
            $message = "请求失败，请稍后再试";
        }
        return $this->fail([$exception->getCode(), $message], null, $data);
    }

    /**
     * 异常信息
     *
     * @param Throwable $e
     *
     * @return array
     */
    protected function renderException(Throwable $e): array
    {
        return [
            'name' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'message' => $e->getMessage(),
            'tables' => [
                'GET Data' => request()->all(),
                'POST Data' => $_POST
            ],
        ];
    }
}
