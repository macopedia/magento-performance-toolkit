<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var Mage_Core_Model_Config_Data $configData */
$configData = Mage::getModel('core/config_data');
$configData->setPath('admin/security/use_form_key')
    ->setScope('default')
    ->setScopeId(0)
    ->setValue(0)
    ->save();

Mage::app()->cleanCache(array(\Mage_Core_Model_Config::CACHE_TAG));
