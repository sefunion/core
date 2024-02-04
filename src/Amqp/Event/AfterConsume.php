<?php

declare(strict_types=1);


namespace Easy\Amqp\Event;

use Hyperf\Amqp\Message\ConsumerMessageInterface;

class AfterConsume
{
    /**
     * @var ConsumerMessageInterface
     */
    public $message;

    public $data;

    public $result;

    public function __construct($message, $data, $result)
    {
        $this->message = $message;
        $this->data = $data;
        $this->result = $result;
    }
}
