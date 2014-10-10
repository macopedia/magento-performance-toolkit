<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** @var \Magento\ToolkitFramework\Application $this */
$categoriesNumber = \Magento\ToolkitFramework\Config::getInstance()->getValue('categories', 30);
$maxNestingLevel = \Magento\ToolkitFramework\Config::getInstance()->getValue('categories_nesting_level', 3);
Mage::init();

/** @var $category \Mage_Catalog_Model_Category */
$category = Mage::getModel('catalog/category');

$groups = array();
$storeGroups = Mage::app()->getGroups();
$i = 0;
foreach ($storeGroups as $storeGroup) {
    $parentCategoryId[$i] = $defaultParentCategoryId[$i] = $storeGroup->getRootCategoryId();
    $nestingLevel[$i] = 1;
    $nestingPath[$i] = "1/$parentCategoryId[$i]";
    $categoryPath[$i] = '';
    $i++;
}
$group_number = 0;
$anchorStep = 2;
$categoryIndex = 1;

while ($categoryIndex <= $categoriesNumber) {
    $category->setId(null)
        ->setName("Category $categoryIndex")
        ->setParentId($parentCategoryId[$group_number])
        ->setPath($nestingPath[$group_number])
        ->setLevel($nestingLevel[$group_number])
        ->setAvailableSortBy('name')
        ->setDefaultSortBy('name')
        ->setIsActive(true)
        //->setIsAnchor($categoryIndex++ % $anchorStep == 0)
        ->save();
    $categoryIndex++;
    $categoryPath[$group_number] .=  '/' . $category->getName();

    if ($nestingLevel[$group_number]++ == $maxNestingLevel) {
        $nestingLevel[$group_number] = 1;
        $parentCategoryId[$group_number] = $defaultParentCategoryId[$group_number];
        $nestingPath[$group_number] = '1';
        $categoryPath[$group_number] = '';
    } else {
        $parentCategoryId[$group_number] = $category->getId();
    }
    $nestingPath[$group_number] .= "/$parentCategoryId[$group_number]";

    $group_number++;
    if ($group_number==count($defaultParentCategoryId)) {
        $group_number = 0;
    }
}


