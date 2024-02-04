<?php


declare(strict_types=1);


namespace Easy\Traits;

use Hyperf\Di\Annotation\Inject;
use Easy\EasyRequest;
use Easy\EasyResponse;
use Psr\Http\Message\ResponseInterface;

trait ControllerTrait
{
    /**
     * Easy 请求处理
     * EasyRequest.
     */
    #[Inject]
    protected EasyRequest $request;

    /**
     * Easy 响应处理
     * EasyResponse.
     */
    #[Inject]
    protected EasyResponse $response;

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function success(null|array|object|string $msgOrData = '', array|object $data = [], int $code = 200): ResponseInterface
    {
        if (is_string($msgOrData) || is_null($msgOrData)) {
            return $this->response->success($msgOrData, $data, $code);
        }
        if (is_array($msgOrData) || is_object($msgOrData)) {
            return $this->response->success(null, $msgOrData, $code);
        }
        return $this->response->success(null, $data, $code);
    }

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function error(string $message = '', int $code = 500, array $data = []): ResponseInterface
    {
        return $this->response->error($message, $code, $data);
    }

    /**
     * 跳转.
     */
    public function redirect(string $toUrl, int $status = 302, string $schema = 'http'): ResponseInterface
    {
        return $this->response->redirect($toUrl, $status, $schema);
    }

    /**
     * 下载文件.
     */
    public function _download(string $filePath, string $name = ''): ResponseInterface
    {
        return $this->response->download($filePath, $name);
    }
}
