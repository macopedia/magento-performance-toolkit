<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     toolkit_framework
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Magento application for performance tests
 */
namespace Magento\ToolkitFramework;

class Application
{
    /**
     * Area code
     */
    const AREA_CODE = 'install';

    /**
     * @var \Magento_Shell
     */
    protected $_shell;

    /**
     * List of fixtures applied to the application
     *
     * @var array
     */
    protected $_fixtures = array();

    /**
     * @var string
     */
    protected $_applicationBaseDir;

    /**
     * @param string $applicationBaseDir
     * @param \Magento_Shell $shell
     */
    public function __construct($applicationBaseDir, \Magento_Shell $shell)
    {
        $this->_applicationBaseDir = $applicationBaseDir;
        $this->_shell = $shell;
    }

    /**
     * Update permissions for `var` directory
     *
     * @return void
     */
    protected function _updateFilesystemPermissions()
    {
        chmod(\Mage::app()->getConfig()->getBaseDir() . DS . 'var', 0777);
    }

    /**
     * Bootstrap application, so it is possible to use its resources
     *
     * @return \Magento\ToolkitFramework\Application
     */
    protected function _bootstrap()
    {
        \Mage::app();
        return $this;
    }

    /**
     * Bootstrap
     *
     * @return Application
     */
    public function bootstrap()
    {
        return $this->_bootstrap();
    }

    /**
     * Run reindex
     *
     * @return Application
     */
    public function reindex()
    {
        $this->_shell->execute(
            'php -f ' . MAGENTO_BASE_DIR . '/shell/indexer.php -- reindexall'
        );
        return $this;
    }

    /**
     * Work on application, so that it has all and only $fixtures applied. May require reinstall, if
     * excessive fixtures has been applied before.
     *
     * @param array $fixtures
     *
     * @return void
     */
    public function applyFixtures(array $fixtures)
    {
        // Apply fixtures
        $fixturesToApply = array_diff($fixtures, $this->_fixtures);
        if (!$fixturesToApply) {
            return;
        }

        $this->_bootstrap();
        foreach ($fixturesToApply as $fixtureFile) {
            $this->applyFixture($fixtureFile);
        }
        $this->_fixtures = $fixtures;

        $this->reindex()
            ->_updateFilesystemPermissions();
    }

    /**
     * Apply fixture file
     *
     * @param string $fixtureFilename
     *
     * @return void
     */
    public function applyFixture($fixtureFilename)
    {
        require $fixtureFilename;
    }

}
