<?php

declare(strict_types=1);


namespace Easy\Amqp\Event;

use Hyperf\Amqp\Message\ConsumerMessageInterface;

class ConsumeEvent
{
    /**
     * @var ConsumerMessageInterface
     */
    public $message;

    public $data;

    public function __construct(ConsumerMessageInterface $message, $data)
    {
        $this->message = $message;
        $this->data = $data;
    }
}
