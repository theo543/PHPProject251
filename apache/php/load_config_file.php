<?php

class ConfigurationException extends Exception {
    public function __construct(string $message) {
        parent::__construct($message);
    }
}

function load_config_file(string $path, array $required_keys = array(), $default = null) {
    if(!file_exists($path)) {
        if($default === null) {
            throw new ConfigurationException("Configuration error. File '$path' does not exist and there are no defaults for this configuration.");
        }
        return $default;
    }
    $config = include($path);
    if(!is_array($config)) {
        throw new ConfigurationException("Configuration error. Successfully loaded '$path' but it does not return an array.");
    }
    if($required_keys !== null) {
        foreach($required_keys as $key) {
            if(!isset($config[$key])) {
                throw new ConfigurationException("Configuration error. Successfully loaded '$path' but the returned array does not contain key '$key'. The required keys are: " . implode(", ", $required_keys));
            }
        }
    }
    return $config;
}
