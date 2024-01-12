<?php


namespace Kindness\ModuleManage\Response;

use Kindness\ModuleManage\Exceptions\BusinessException;
use support\Response;

trait ApiResponse
{
    /**
     * 成功
     * @param null $data
     * @param array $codeResponse
     * @return Response
     */
    public function success($data = null, array $codeResponse = ResponseEnum::HTTP_OK): Response
    {
        return $this->jsonResponse('success', $codeResponse, $data, null);
    }

    /**
     * 失败
     * @param array $codeResponse
     * @param null $data
     * @param null $error
     * @return Response
     */
    public function fail(array $codeResponse = ResponseEnum::HTTP_ERROR, $data = null, $error = null): Response
    {
        return $this->jsonResponse('fail', $codeResponse, $data, $error);
    }

    /**
     * json响应
     * @param $status
     * @param $codeResponse
     * @param $data
     * @param $error
     * @return Response
     */
    private function jsonResponse($status, $codeResponse, $data, $error): Response
    {
        list($code, $message) = $codeResponse;
        return json([
            'status' => $status,
            'code' => $code,
            'message' => $message,
            'data' => $data ?? null,
            'error' => $error,
        ]);
    }

    /**
     * 业务异常返回
     * @param array $codeResponse
     * @param string $info
     * @throws BusinessException
     */
    public function throwBusinessException(array $codeResponse = ResponseEnum::HTTP_ERROR, string $info = '')
    {
        throw new BusinessException($codeResponse, $info);
    }
}
