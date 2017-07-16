<?php

namespace PhpKafkaConsumerFramework\Consumer;

use PhpKafkaConsumerFramework\Configuration\ConfigurationInterface;
use PhpKafkaConsumerFramework\Processor\MessageProcessorInterface;

class BaseConsumer implements ConsumerInterface
{
    /**
     * @var ConfigurationInterface
     */
    protected $configuration;

    /**
     * @var array
     */
    protected $topics = [];

    /**
     * @var array<\RdKafka\TopicPartition>
     */
    protected $partitions = [];

    /**
     * @var array<MessageProcessorInterface>
     */
    protected $processors = [];

    /**
     * @var \RdKafka\KafkaConsumer
     */
    protected $kafkaConsumer;

    /**
     * BaseConsumer constructor.
     * @param ConfigurationInterface $configuration
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
        $this->topics = $configuration->getSubscribedTopics();
        $this->partitions = $configuration->getAssignedPartitions();
        $this->kafkaConsumer = new \RdKafka\KafkaConsumer($this->configuration->getConsumerConf());
    }

    /**
     * @param string $topic
     */
    public function addTopic($topic)
    {
        $this->topics[] = $topic;
    }

    /**
     * @param string $topic
     * @param int $partition
     */
    public function addPartition($topic, $partition = 0)
    {
        $this->partitions[] = new \RdKafka\TopicPartition($topic, $partition);
    }

    /**
     * @param string $topic
     * @param MessageProcessorInterface $processor
     */
    public function setProcessor($topic, MessageProcessorInterface $processor)
    {
        $this->processors[$topic] = $processor;
    }

    /**
     * @param string $topic
     * @return MessageProcessorInterface|null
     */
    public function getProcessor($topic)
    {
        return isset($this->processors[$topic]) ? $this->processors[$topic] : null;
    }

    /**
     * @param bool $ignoreExceptions
     * @param bool $autoCommit
     *
     * @throws \Exception
     */
    public function consume($ignoreExceptions = false, $autoCommit = true)
    {
        $this->kafkaConsumer->subscribe($this->topics);
        $this->kafkaConsumer->assign($this->partitions);

        try {
            while (true) {
                $message = $this->kafkaConsumer->consume($this->configuration->getConsumptionTimeoutMs());
                switch ($message->err) {
                    case RD_KAFKA_RESP_ERR_NO_ERROR:
                        $this->processors[$message->topic_name]->process($message);
                        if ($autoCommit) {
                            $this->kafkaConsumer->commit($message);
                        }
                        break;
                    case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                        echo "No more messages; will wait for more\n";
                        break;
                    case RD_KAFKA_RESP_ERR__TIMED_OUT:
                        echo "Timed out\n";
                        break;
                    default:
                        throw new \Exception($message->errstr(), $message->err);
                        break;
                }
            }
        } catch (\Exception $e) {
            if (false === $ignoreExceptions) throw $e;
        }
    }
}