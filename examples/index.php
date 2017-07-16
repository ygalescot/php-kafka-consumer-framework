<?php

require_once ('../vendor/autoload.php');

use PhpKafkaConsumerFramework\Configuration\ConfigurationLoader;
use PhpKafkaConsumerFramework\Factory\ConsumerFactory;
use PhpKafkaConsumerFramework\Processor\ExampleProcessor;

$configurationLoader = new ConfigurationLoader('../config/config.yml');

$consumer = (new ConsumerFactory($configurationLoader))->createConsumer('my_consumer');
$consumer->setProcessor('my_topic', new ExampleProcessor());
$consumer->consume();