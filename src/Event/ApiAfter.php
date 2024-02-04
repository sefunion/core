<?php

declare(strict_types=1);


namespace Easy\Event;

use Psr\Http\Message\ResponseInterface;

class ApiAfter
{
    protected ?array $apiData;

    protected ResponseInterface $result;

    public function __construct(?array $apiData, ResponseInterface $result)
    {
        $this->apiData = $apiData;
        $this->result = $result;
    }

    public function getApiData(): ?array
    {
        return $this->apiData;
    }

    public function getResult(): ResponseInterface
    {
        return $this->result;
    }
}
