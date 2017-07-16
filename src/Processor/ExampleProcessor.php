<?php

namespace PhpKafkaConsumerFramework\Processor;

use RdKafka\Message;

class ExampleProcessor implements MessageProcessorInterface
{
    /**
     * @param Message $message
     */
    public function process(Message $message)
    {
        // TODO: Implement process() method.
    }
}