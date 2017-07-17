<?php

namespace PhpKafkaConsumerFramework\Consumer;

use PhpKafkaConsumerFramework\Configuration\ConfigurationInterface;
use PhpKafkaConsumerFramework\Processor\MessageProcessorInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use \RdKafka\Message;

/**
 * Class BaseConsumer
 * @package PhpKafkaConsumerFramework\Consumer
 *
 * This is our BaseConsumer class used to generate all other consumers.
 */
class BaseConsumer implements ConsumerInterface
{
    /**
     * @var ConfigurationInterface
     */
    protected $configuration;

    /**
     * @var LoggerInterface
     */
    protected $logger;

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
     * @param LoggerInterface|null $logger
     */
    public function __construct(ConfigurationInterface $configuration, LoggerInterface $logger = null)
    {
        $this->configuration = $configuration;
        $this->logger = (null !== $logger) ? $logger : new NullLogger();
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
                $this->logger->info('Consuming message...');
                $message = $this->kafkaConsumer->consume($this->configuration->getConsumptionTimeoutMs());
                switch ($message->err) {
                    case RD_KAFKA_RESP_ERR_NO_ERROR:
                        $this->logger->info('Processing message...');
                        $this->handleMessageProcessing($message);
                        if ($autoCommit) {
                            $this->commit($message);
                        }
                        break;
                    case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                        $this->logger->info("No more messages; will wait for more.");
                        echo "No more messages; will wait for more\n";
                        break;
                    case RD_KAFKA_RESP_ERR__TIMED_OUT:
                        $this->logger->info("Timed out");
                        echo "Timed out\n";
                        break;
                    default:
                        $this->logger->error($message->errstr());
                        throw new \Exception($message->errstr(), $message->err);
                        break;
                }
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            if (false === $ignoreExceptions) throw $e;
        }
    }

    /**
     * @param Message $message
     *
     * @throws \Exception
     */
    protected function handleMessageProcessing(Message $message)
    {
        if (null === $this->getProcessor($message->topic_name)) {
            throw new \Exception(sprintf('No processor found for %s topic messages.', $message->topic_name));
        }

        $this->getProcessor($message->topic_name)->process($message);
    }

    /**
     * @param Message $message
     */
    public function commit(Message $message)
    {
        $this->kafkaConsumer->commit($message);
    }
}