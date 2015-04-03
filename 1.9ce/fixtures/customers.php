<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** @var \Magento\ToolkitFramework\Application $this */
$customersNumber = \Magento\ToolkitFramework\Config::getInstance()->getValue('customers', 20);

/** @var $category \Mage_Catalog_Model_Category */
$category = Mage::getModel('catalog/category');

$result = array();
//Get all websites
$websites = Mage::app()->getWebsites();
foreach($websites as $website) {
    $result[] = $website->getCode();
}
$result = array_values($result);

$productWebsite = function ($index) use ($result) {
    return $result[$index % count($result)];
};

$pattern = array(
    'email'                       => 'user_%s@example.com',
    '_website'                    => $productWebsite,
    '_store'                      => '',
    'confirmation'                => '',
    'created_at'                  => '30-08-2012 17:43',
    'created_in'                  => 'Default',
    'disable_auto_group_change'   => '0',
    'dob'                         => '12-10-1991',
    'firstname'                   => 'Firstname',
    'gender'                      => '',
    'group_id'                    => '1',
    'lastname'                    => 'Lastname',
    'middlename'                  => '',
    'password_hash'               => 'de258d78b1bda6e0742d6262669ceb87a8099b8e1ebe559b247163e732b562b7:1c',
    'prefix'                      => '',
    'reward_update_notification'  => '1',
    'reward_warning_notification' => '1',
    'rp_token'                    => '',
    'rp_token_created_at'         => '',
    'store_id'                    => '0',
    'suffix'                      => '',
    'taxvat'                      => '',
    'website_id'                  => '1',
    'password'                    => '123123q',
    '_address_city'               => 'Fayetteville',
    '_address_company'            => '',
    '_address_country_id'         => 'US',
    '_address_fax'                => '',
    '_address_firstname'          => 'Anthony',
    '_address_lastname'           => 'Nealy',
    '_address_middlename'         => '',
    '_address_postcode'           => '123123',
    '_address_prefix'             => '',
    '_address_region'             => 'Arkansas',
    '_address_street'             => '123 Freedom Blvd. #123',
    '_address_suffix'             => '',
    '_address_telephone'          => '022-333-4455',
    '_address_vat_id'             => '',
    '_address_default_billing_'   => '1',
    '_address_default_shipping_'  => '1'
);
$generator = new \Magento\ToolkitFramework\ImportExport\Fixture\Generator($pattern, $customersNumber);
/** @var $import \Mage_ImportExport_Model_Import */
$import = Mage::getModel('importexport/import');
$import->setEntity('customer');
$import->setBehavior('append');
// it is not obvious, but the validateSource() will actually save import queue data to DB
$import->validateSource((string)$generator);
// this converts import queue into actual entities
$import->importSource();
