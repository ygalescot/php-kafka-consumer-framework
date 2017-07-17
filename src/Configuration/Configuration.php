<?php

namespace PhpKafkaConsumerFramework\Configuration;

/**
 * Class Configuration
 * @package PhpKafkaConsumerFramework\Configuration
 *
 * This class handles the configuration of a consumer.
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @var array
     */
    protected $consumerConfigData;

    /**
     * @var \RdKafka\Conf
     */
    protected $consumerConf;

    /**
     * @var \RdKafka\TopicConf
     */
    protected $topicConf;

    /**
     * Configuration constructor.
     * @param array $consumerConfigData
     */
    public function __construct(array $consumerConfigData)
    {
        $this->consumerConfigData = $consumerConfigData;
        $this->consumerConf = new \RdKafka\Conf();
        $this->topicConf = new \RdKafka\TopicConf();
    }

    /**
     * @return \RdKafka\Conf
     */
    public function getConsumerConf()
    {
        $this->consumerConf->set('group.id', $this->consumerConfigData['group_id']);
        $this->consumerConf->set('metadata.broker.list', $this->consumerConfigData['brokers']);

        $this->consumerConf->setDefaultTopicConf($this->getTopicConf());
        $this->consumerConf->setRebalanceCb(function(){
            //TODO: Implement the rebalance callback
        });

        return $this->consumerConf;
    }

    /**
     * @return \RdKafka\TopicConf
     */
    public function getTopicConf()
    {
        // Set where to start consuming messages when there is no initial offset in
        // offset store or the desired offset is out of range.
        // 'smallest': start from the beginning
        $this->topicConf->set('auto.offset.reset', $this->consumerConfigData['topic_auto_offset_reset']);

        return $this->topicConf;
    }

    /**
     * @return array
     */
    public function getSubscribedTopics()
    {
        // TODO: retrieve the list of topics registered for the consumer
        return [];
    }

    /**
     * @return array<\RdKafka\TopicPartition>
     */
    public function getAssignedPartitions()
    {
        // TODO: retrieve the list of partitions in each topic
        return [];
    }

    /**
     * @return integer
     */
    public function getConsumptionTimeoutMs()
    {
        return $this->consumerConfigData['consumption_timeout_ms'];
    }
}