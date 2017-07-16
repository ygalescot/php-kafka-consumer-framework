<?php

namespace PhpKafkaConsumerFramework\Configuration;

interface ConfigurationInterface
{
    /**
     * @return \RdKafka\Conf
     */
    public function getConsumerConf();

    /**
     * @return \RdKafka\TopicConf
     */
    public function getTopicConf();

    /**
     * @return array
     */
    public function getSubscribedTopics();

    /**
     * @return array<\RdKafka\TopicPartition>
     */
    public function getAssignedPartitions();

    /**
     * @return integer
     */
    public function getConsumptionTimeoutMs();
}