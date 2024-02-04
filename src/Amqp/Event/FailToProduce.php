<?php

declare(strict_types=1);


namespace Easy\Amqp\Event;

use Hyperf\Amqp\Message\ProducerMessageInterface;

class FailToProduce extends ConsumeEvent
{
    /**
     * @var \Throwable
     */
    public $throwable;

    public function __construct(ProducerMessageInterface $producer, \Throwable $throwable)
    {
        $this->throwable = $throwable;
    }

    public function getThrowable(): \Throwable
    {
        return $this->throwable;
    }
}
