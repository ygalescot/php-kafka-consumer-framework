<?php

namespace PhpKafkaConsumerFramework\Processor;

use RdKafka\Message;

/**
 * Class ExampleProcessor
 * @package PhpKafkaConsumerFramework\Processor
 *
 * This is an example class of a processor used to process an RdKafka Message.
 */
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