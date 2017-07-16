<?php

namespace PhpKafkaConsumerFramework\Configuration;

use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class ConfigurationLoader
{
    /**
     * @var string
     */
    protected $configFile;

    /**
     * ConfigurationLoader constructor.
     * @param string $configFile
     */
    public function __construct($configFile)
    {
        $this->configFile = $configFile;
    }

    /**
     * @param string $consumerName
     * @return ConfigurationInterface
     */
    public function loadConfiguration($consumerName)
    {
        $configData = $this->parseConfigFile();
        return new Configuration($configData[$consumerName]);
    }

    /**
     * @return array
     */
    protected function parseConfigFile()
    {
        try {
            return Yaml::parse(file_get_contents($this->configFile));
        } catch (ParseException $e) {
            printf("Unable to parse the YAML string: %s", $e->getMessage());
        }
    }
}