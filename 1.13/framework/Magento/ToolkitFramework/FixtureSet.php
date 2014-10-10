<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ToolkitFramework;

class FixtureSet
{
    /**
     * Configuration array
     *
     * @var array
     */
    protected $_fixtures = array();

    /**
     * Get config instance
     *
     * @return \Magento\ToolkitFramework\FixtureSet
     */
    public static function getInstance()
    {
        static $instance;
        if (!$instance instanceof static) {
            $instance = new static(__DIR__ . '/../../fixtures.xml');
        }
        return $instance;
    }

    /**
     * Constructor
     *
     * @param string $filename
     * @throws \Exception
     */
    public function __construct($filename)
    {
        if (!is_readable($filename)) {
            throw new \Exception("Fixtures set file `{$filename}` is not readable or does not exists.");
        }
        $parser = new \Mage_Xml_Parser();
        $this->_fixtures = $parser->load($filename)->xmlToArray();
    }

    /**
     * Get fixtures array
     *
     * @param array $default
     *
     * @return array
     */
    public function getFixtures($default = array())
    {
        return isset($this->_fixtures['fixtures']) ? $this->_fixtures['fixtures'] : $default;
    }
}
