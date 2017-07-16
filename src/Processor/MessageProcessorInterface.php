<?php

namespace PhpKafkaConsumerFramework\Processor;

use RdKafka\Message;

interface MessageProcessorInterface
{
    /**
     * @param Message $message
     * @return void
     */
    public function process(Message $message);
}