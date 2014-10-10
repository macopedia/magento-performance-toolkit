<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** @var \Magento\ToolkitFramework\Application $this */

/**
 * @var \Mage_Core_Model_Config_Data $configData
 */
$configData = Mage::getModel('core/config_data');
$configData->setPath('carriers/flatrate/active')
    ->setScope('default')
    ->setScopeId(0)
    ->setValue(1)
    ->save();

Mage::app()->cleanCache(array(\Mage_Core_Model_Config::CACHE_TAG));
