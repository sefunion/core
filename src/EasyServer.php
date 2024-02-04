<?php


declare(strict_types=1);


namespace Easy;

use Hyperf\HttpServer\Server;

class EasyServer extends Server
{
    protected ?string $serverName = 'EasyCMF';

    protected $routes;

    public function onRequest($request, $response): void
    {
        parent::onRequest($request, $response);
        $this->bootstrap();
    }

    /**
     * EasyServer bootstrap.
     */
    protected function bootstrap(): void {}
}
