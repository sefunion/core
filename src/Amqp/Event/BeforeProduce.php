<?php

declare(strict_types=1);


namespace Easy\Amqp\Event;

use Hyperf\Amqp\Message\ProducerMessageInterface;

class BeforeProduce
{
    public $producer;

    public $delayTime;

    public function __construct(ProducerMessageInterface $producer, int $delayTime)
    {
        $this->producer = $producer;
        $this->delayTime = $delayTime;
    }
}
