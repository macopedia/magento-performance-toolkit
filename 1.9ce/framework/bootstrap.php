<?php
/**
 * Toolkit framework bootstrap script
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     performance_toolkit_framework
 * @copyright   {copyright}
 * @license     {license_link}
 */

$toolkitBaseDir = realpath(__DIR__ . '/..');
$magentoBaseDir = realpath($toolkitBaseDir . '/../../../');

define('MAGENTO_BASE_DIR', $magentoBaseDir);
define('TOOLKIT_BASE_DIR', $toolkitBaseDir);
define('DEFAULT_TEMP_DIR', 'tmp');

/**
 * Error reporting
 */
error_reporting(E_ALL | E_STRICT);

/**
 * Compilation includes configuration file
 */
define('MAGENTO_ROOT', getcwd());

$mageFilename = __DIR__ . '/../../../../app/Mage.php';

require_once $mageFilename;

#Varien_Profiler::enable();

if (isset($_SERVER['MAGE_IS_DEVELOPER_MODE'])) {
    Mage::setIsDeveloperMode(true);
}

#ini_set('display_errors', 1);

umask(0);

set_include_path(get_include_path() . PATH_SEPARATOR . "$magentoBaseDir/dev/tools/performance_toolkit/framework");
spl_autoload_register(function ($name) {
    $classFilePath = str_replace('\\', DIRECTORY_SEPARATOR, $name);
    $classFilePath = __DIR__ . DIRECTORY_SEPARATOR . $classFilePath . '.php';
    if (file_exists($classFilePath)) {
        return include $classFilePath;
    }
}, true, true);

return $magentoBaseDir;
