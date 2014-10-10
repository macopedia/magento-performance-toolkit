<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ToolkitFramework;

class Config
{
    /**
     * Configuration array
     *
     * @var array
     */
    protected $_config = array();

    /**
     * Labels for config values
     *
     * @var array
     */
    protected $_labels = array();

    /**
     * Get config instance
     *
     * @return \Magento\ToolkitFramework\Config
     */
    public static function getInstance()
    {
        static $instance;
        if (!$instance instanceof static) {
            $instance = new static;
        }
        return $instance;
    }

    /**
     * Load config from file
     *
     * @param string $filename
     * @throws \Exception
     *
     * @return void
     */
    public function loadConfig($filename)
    {
        if (!is_readable($filename)) {
            throw new \Exception("Profile configuration file `{$filename}` is not readable or does not exists.");
        }
        $parser = new \Mage_Xml_Parser();
        $this->_config = $parser->load($filename)->xmlToArray();
    }

    /**
     * Load labels
     *
     * @param string $filename
     * @throws \Exception
     *
     * @return void
     */
    public function loadLabels($filename)
    {
        if (!is_readable($filename)) {
            throw new \Exception("Labels file `{$filename}` is not readable or does not exists.");
        }
        $parser = new \Mage_Xml_Parser();
        $this->_labels = $parser->load($filename)->xmlToArray();
    }

    /**
     * Get labels array
     *
     * @return array
     */
    public function getLabels()
    {
        return isset($this->_labels['config']['labels']) ? $this->_labels['config']['labels'] : array();
    }

    /**
     * Get profile configuration value
     *
     * @param string $key
     * @param null|mixed $default
     *
     * @return mixed
     */
    public function getValue($key, $default = null)
    {
        return isset($this->_config['config']['profile'][$key]) ? $this->_config['config']['profile'][$key] : $default;
    }
}
