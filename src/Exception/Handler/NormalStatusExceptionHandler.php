<?php


declare(strict_types=1);


namespace Easy\Exception\Handler;

use Hyperf\Codec\Json;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Easy\Exception\NormalStatusException;
use Easy\Log\RequestIdHolder;
use Psr\Http\Message\ResponseInterface;

/**
 * Class DataNotFoundExceptionHandler.
 */
class NormalStatusExceptionHandler extends ExceptionHandler
{
    public function handle(\Throwable $throwable, ResponseInterface $response): ResponseInterface
    {
        $this->stopPropagation();
        $format = [
            'requestId' => RequestIdHolder::getId(),
            'success' => false,
            'message' => $throwable->getMessage(),
        ];
        if ($throwable->getCode() != 200 && $throwable->getCode() != 0) {
            $format['code'] = $throwable->getCode();
        }
        //        logger('Exception log')->debug($throwable->getMessage());
        return $response->withHeader('Server', 'EasyCMF')
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Methods', 'GET,PUT,POST,DELETE,OPTIONS')
            ->withHeader('Access-Control-Allow-Credentials', 'true')
            ->withHeader('Access-Control-Allow-Headers', 'accept-language,authorization,lang,uid,token,Keep-Alive,User-Agent,Cache-Control,Content-Type')
            ->withAddedHeader('content-type', 'application/json; charset=utf-8')
            ->withBody(new SwooleStream(Json::encode($format)));
    }

    public function isValid(\Throwable $throwable): bool
    {
        return $throwable instanceof NormalStatusException;
    }
}
