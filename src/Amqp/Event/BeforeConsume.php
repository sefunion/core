<?php

declare(strict_types=1);


namespace Easy\Amqp\Event;

use Hyperf\Amqp\Message\ConsumerMessageInterface;

class BeforeConsume
{
    /**
     * @var ConsumerMessageInterface
     */
    public $message;

    public $data;

    public function __construct($message, $data)
    {
        $this->message = $message;
        $this->data = $data;
    }
}
