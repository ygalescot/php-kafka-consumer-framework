<?php

namespace PhpKafkaConsumerFramework\Configuration;

class Configuration implements ConfigurationInterface
{
    /**
     * @var array
     */
    protected $consumerConfigData;

    /**
     * @var \RdKafka\Conf
     */
    protected $consumerConfig;

    /**
     * @var \RdKafka\TopicConf
     */
    protected $topicConfig;

    public function __construct(array $consumerConfigData)
    {
        $this->consumerConfigData = $consumerConfigData;
        $this->consumerConfig = new \RdKafka\Conf();
        $this->topicConfig = new \RdKafka\TopicConf();
    }

    /**
     * @return \RdKafka\Conf
     */
    public function getConsumerConf()
    {
        $this->consumerConfig->set('group.id', $this->consumerConfigData['group_id']);
        $this->consumerConfig->set('metadata.broker.list', $this->consumerConfigData['brokers']);

        $this->consumerConfig->setDefaultTopicConf($this->getTopicConf());
        $this->consumerConfig->setRebalanceCb(function(){});

        return $this->consumerConfig;
    }

    /**
     * @return \RdKafka\TopicConf
     */
    public function getTopicConf()
    {
        // Set where to start consuming messages when there is no initial offset in
        // offset store or the desired offset is out of range.
        // 'smallest': start from the beginning
        $this->topicConfig->set('auto.offset.reset', 'smallest');

        return $this->topicConfig;
    }

    /**
     * @return array
     */
    public function getSubscribedTopics()
    {
        return [];
    }

    /**
     * @return array<\RdKafka\TopicPartition>
     */
    public function getAssignedPartitions()
    {
        return [];
    }

    /**
     * @return integer
     */
    public function getConsumptionTimeoutMs()
    {
        return;
    }
}