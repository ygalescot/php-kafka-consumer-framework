<?php

namespace PhpKafkaConsumerFramework\Consumer;

use PhpKafkaConsumerFramework\Processor\MessageProcessorInterface;

interface ConsumerInterface
{
    /**
     * @param string $topic
     */
    public function addTopic($topic);

    /**
     * @param string $topic
     * @param int $partition
     */
    public function addPartition($topic, $partition = 0);

    /**
     * @param string $topic
     * @param MessageProcessorInterface $processor
     */
    public function setProcessor($topic, MessageProcessorInterface $processor);

    /**
     * @param string $topic
     * @return MessageProcessorInterface|null
     */
    public function getProcessor($topic);

    /**
     * @param bool $ignoreExceptions
     * @param bool $autoCommit
     */
    public function consume($ignoreExceptions = false, $autoCommit = true);

    /**
     * @param Message $message
     */
    public function commit(Message $message);
}