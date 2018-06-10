<?php

/**
 * TinyFrame
 *
 * @copyright Copyright (c) 2018 Michael Gutierrez
 * @license   MIT License
 */

namespace TinyFrame\Lib;

/**
 * Configuration class. This class expects a file that is parsable by parse_ini_file. Other config files can be
 * referenced for loading if they are set in the key environment.load_config and exist. Config files other than the
 * base can be set when the object is instantiated or by calling loadConfigFomFile. The recursive flag must be set to
 * true to load files recursively. By default recursive loading is off.
 *
 * */
class Configuration
{

    /**
     * Default config file location
     * @var string
     */
    const DEFAULT_DIRECTORY = __DIR__ . '/../../';

    /**
     * Default config file name
     * @var string
     */
    const DEFAULT_CONFIG = 'config.ini';

    private $configFile;
    private $config = Array();

    /**
     * Instantiate config file. Use default if no config file is provided. Parse file and return config object.
     *
     * @param string $configFile
     * @param bool $recursive
     * */
    public function __construct($configFile = null, $recursive = false)
    {
        if(is_null($configFile))
        {
            $this->configFile = realpath(self::DEFAULT_DIRECTORY . self::DEFAULT_CONFIG, $recursive);
        }
        else
        {
            $this->configFile = realpath($configFile, $recursive);
        }

        $this->loadConfigFromFile($this->configFile);
    }


    /**
     * Load a file. Useful if an object needs a different or additional configuration not in the default config such
     * as a batch processor or an image manipulator
     *
     * @param string $configFile. Absolute path to file to be loaded
     * @param bool $recursive
     * */
    public function loadConfigFromFile($configFile, $recursive = false)
    {
        $config = parse_ini_file($configFile, true);
        $this->config = array_replace_recursive($config, $this->config); //in case you load more files

        //Oh you included more configs. Let's load those too
        if($recursive)
        {
            if (isset($this->config['environment']['load_config']) && $secondConfigFile = $this->config['environment']['load_config'])
            {
                $pathInfo = pathinfo($configFile);
                $path = realpath($pathInfo['dirname'] . $secondConfigFile);
                $this->loadConfigFromFile($path, true);
            }
        }
    }

    /**
     * Get value from config value. Keys are expected in dot notation as in section.setting
     *
     * @param string $key
     * @return string
     * */
    public function getValue($key)
    {
        list($section, $var) = explode('.', $key, 2);
        return $this->config[$section][$var] ? : false;
    }
}