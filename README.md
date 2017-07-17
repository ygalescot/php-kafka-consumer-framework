# An example of a Kafka consumer framework for PHP

This framework is based on this PHP Kafka client 
[https://github.com/arnaud-lb/php-rdkafka](https://github.com/arnaud-lb/php-rdkafka)

Note: This repository is just an example of the structure of the framework.
It is not fully implemented and not functional.

## Principles

This frameworks allows the creation of consumers based on the provided the configuration.

You can create as many consumers as you want.

Each consumer can handle multiple topics and for each topic, multiple partitions.

## Architecture

You can configure consumers in the config/config.yml file.

The creation of a consumer is handled by the ConsumerFactory.

A ConfigurationLoader parses the config.yml file. It is injected into the factory in order to load a
consumer specific Configuration file.

Based on this Configuration the factory will create a new ConsumerInterface instance based on the BaseConsumer class.

The BaseConsumer class is a customized Kafka consumer that can:
- handle an injected Configuration
- add topics to consume
- add partitions to assign
- define a processor for each topic that will process the consumed message
- consume from the subscribed topics with their assigned partitions.
- and commit message manually or let the consumer auto commit by default

The handling of a topic message is performed by a MessageProcessorInterface instance added to the consumer.

## How it works

**1. Define your config in the config/config.yml file:**

```
php_kafka_consumer_framework:
    consumers:
        my_consumer:
            group_id: myConsumerGroup
            brokers: [127.0.0.1, 10.0.25.4]
            consumption_timeout_ms: 1200000
            topic_auto_offset_reset: smallest
            topics:
                my_topic:
                    partitions: [0, 2]
```

**2. Instantiate the ConfigurationLoader and the Factory**

```
$configurationLoader = new ConfigurationLoader('../config/config.yml');
$consumerFactory = new ConsumerFactory($configurationLoader)
```

**3. Create the consumer**

```
$consumer = $consumerFactory->createConsumer('consumer_name');
```

The factory will create a new consumer following the config provided in the config.yml for your consumer name.

The consumer is flexible to adding new topics or partitions after its instantiation.


**4. Set a processor for each topic of your consumer.**

You can create your own processors.
Each processor must implement MessageProcessorInterface.

```
$consumer->setProcessor('my_topic', new ExampleProcessor());
```

**5. Consume topics**

```
$consumer->consume();
```

This method will handle the consumption loop 
and will process the messages automatically thanks to the previously provided message processors.

Note: The consume() method has 2 arguments:
- ignoreExceptions: (default: false) where you can ignore exceptions occurring during consumption
- autoCommit: (default: true) where you choose to save or not the offset 
