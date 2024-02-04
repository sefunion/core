<?php

declare(strict_types=1);


namespace Easy\Log\Processor;

use Hyperf\Coroutine\Coroutine;
use Easy\Log\RequestIdHolder;
use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;

class SnowflakeRequestIdProcessor implements ProcessorInterface
{
    public function __invoke(array|LogRecord $record)
    {
        RequestIdHolder::setType('snowflake');
        $record['extra']['request_id'] = RequestIdHolder::getId();
        $record['extra']['coroutine_id'] = Coroutine::id();
        return $record;
    }
}
