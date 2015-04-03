<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** @var \Magento\ToolkitFramework\Application $this */
$websitesCount = \Magento\ToolkitFramework\Config::getInstance()->getValue('websites', 1);
$storeGroupsCount = \Magento\ToolkitFramework\Config::getInstance()->getValue('store_groups', 1);
$storesCount = \Magento\ToolkitFramework\Config::getInstance()->getValue('store_views', 1);

$category =  Mage::getModel('catalog/category');

$defaultWebsite =  Mage::app()->getWebsite();
$defaultStoreGroup =  Mage::app()->getGroup();
$defaultStoreView =  Mage::app()->getDefaultStoreView();

$default_parent_category_id =  Mage::app()->getStore()->getRootCategoryId();

$default_website_id = $defaultWebsite->getId();
$default_store_group_id = $defaultStoreGroup->getId();
$default_store_view_id = $defaultStoreView->getId();

$websites_id = array();
$groups_id = array();

//Create $websitesCount websites
for ($i = 0; $i < $websitesCount; $i++) {
    $website_id = null;
    if ($i == 0) {
        $website_id = $default_website_id;
    }
    $website = clone $defaultWebsite;
    $websiteCode = sprintf('website_%d', $i+1);
    $websiteName = sprintf('Website %d', $i+1);
    $website->addData(array(
        'website_id' => $website_id,
        'code'     => $websiteCode,
        'name'     => $websiteName
    ));
    $website->save();
    $websites_id[$i] = $website->getId();
    usleep(20);
}

//Create $storeGroupsCount websites
$website_number = 0;
for ($i = 0; $i < $storeGroupsCount; $i++) {
    $website_id = $websites_id[$website_number];
    $group_id = null;
    $parent_category_id = null;
    $category_path = '1';

    $storeGroupName = sprintf('Store Group %d - website_id_%d', $i+1, $website_id);

    if ($i == 0 && $website_id == $default_website_id) {
        $group_id = $default_store_group_id;
        $parent_category_id = $default_parent_category_id;
        $category_path = '1/' . $default_parent_category_id;
    }

    $category->setId($parent_category_id)
        ->setName("Category $storeGroupName")
        ->setPath($category_path)
        ->setLevel(1)
        ->setAvailableSortBy('name')
        ->setDefaultSortBy('name')
        ->setIsActive(true)
        ->save();

    $storeGroup = clone $defaultStoreGroup;
    $storeGroup->addData(array(
        'group_id'          => $group_id,
        'website_id'        => $website_id,
        'name'              => $storeGroupName,
        'root_category_id'  => $category->getId()
    ));
    $storeGroup->save();
    $groups_id[$website_id][] = $storeGroup->getId();

    $website_number++;
    if ($website_number==count($websites_id)) {
        $website_number = 0;
    }
    usleep(20);
}

//Create $storesCount stores
$website_number = 0;
$group_number = 0;
for ($i = 0; $i < $storesCount; $i++) {
    $website_id = $websites_id[$website_number];
    $group_id = $groups_id[$website_id][$group_number];
    $store_id = null;
    if ($i == 0 && $group_id == $default_store_group_id) {
        $store_id = $default_store_view_id;
    }
    $store = clone $defaultStoreView;
    $storeCode = sprintf('store_view_%d_w_%d_g_%d', $i+1, $website_id, $group_id);
    $storeName = sprintf('Store view %d - website_id_%d - group_id_%d', $i+1, $website_id, $group_id);
    $store->addData(array(
        'store_id'      => $store_id,
        'code'          => $storeCode,
        'name'          => $storeName,
        'website_id'    => $website_id,
        'group_id'      => $group_id
    ));
    $store->save();

    $group_number++;
    if ($group_number==count($groups_id[$website_id])) {
        $group_number = 0;
        $website_number++;
        if ($website_number==count($websites_id)) {
            $website_number = 0;
        }
    }
    usleep(20);
}
