<?php

namespace PhpKafkaConsumerFramework\Factory;

use PhpKafkaConsumerFramework\Configuration\ConfigurationLoader;
use PhpKafkaConsumerFramework\Consumer\BaseConsumer;
use PhpKafkaConsumerFramework\Consumer\ConsumerInterface;

class ConsumerFactory
{
    /**
     * @var ConfigurationLoader
     */
    protected $configurationLoader;

    /**
     * ConsumerFactory constructor.
     * @param ConfigurationLoader $configurationLoader
     */
    public function __construct(ConfigurationLoader $configurationLoader)
    {
        $this->configurationLoader = $configurationLoader;
    }

    /**
     * @param $consumerName
     * @return ConsumerInterface
     */
    public function createConsumer($consumerName)
    {
        $consumerConfiguration = $this->configurationLoader->loadConfiguration($consumerName);
        return new BaseConsumer($consumerConfiguration);
    }
}