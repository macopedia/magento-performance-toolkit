<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** @var \Magento\ToolkitFramework\Application $this */
$catalogPriceRulesCount = \Magento\ToolkitFramework\Config::getInstance()->getValue('catalog_price_rules', 10);

$model =  Mage::getModel('catalogrule/rule');
$category =  Mage::getModel('catalog/category');

//Get all websites
$categories_array = array();
$websites = Mage::app()->getWebsites();
foreach($websites as $website) {
    //Get all groups
    $website_groups = $website->getGroups();
    foreach($website_groups as $website_group) {
        $website_group_root_category = $website_group->getRootCategoryId();
        $category->load($website_group_root_category);
        $categoryResource = $category->getResource();
        //Get all categories
        $results_categories = $categoryResource->getAllChildren($category);
        foreach ($results_categories as $results_category) {
            $category->load($results_category);
            $structure = explode('/', $category->getPath());
            if (count($structure) > 2) {
                $categories_array[] = array($category->getId(), $website->getId());
            }
        }
    }
}
asort($categories_array);
$categories_array = array_values($categories_array);
$idField = $model->getIdFieldName();


for ($i = 0; $i < $catalogPriceRulesCount; $i++) {
    $ruleName = sprintf('Catalog Price Rule %1$d', $i);
    $data = array(
        $idField                => null,
        'name'                  => $ruleName,
        'description'           => '',
        'is_active'             => '1',
        'website_ids'           => $categories_array[$i % count($categories_array)][1],
        'customer_group_ids'    => array (
            0 => '0',
            1 => '1',
            2 => '2',
            3 => '3',
        ),
        'from_date'             => '',
        'to_date'               => '',
        'sort_order'            => '',
        'rule'                  => array (
            'conditions' =>
            array (
                1 =>
                array (
                    'type' => 'catalogrule/rule_condition_combine',
                    'aggregator' => 'all',
                    'value' => '1',
                    'new_child' => '',
                ),
                '1--1' =>
                array (
                    'type' => 'catalogrule/rule_condition_product',
                    'attribute' => 'category_ids',
                    'operator' => '==',
                    'value' => $categories_array[$i % count($categories_array)][0],
                ),
            )
        ),
        'simple_action'             => 'by_percent',
        'discount_amount'           => '15',
        'sub_is_enable'              => '0',
        'sub_simple_action'             => 'by_percent',
        'sub_discount_amount'         => '0',
        'stop_rules_processing'      => '0',
        'page'                      => '1',
        'limit'                     => '20',
        'in_banners'                => '1',
        'banner_id'                 => array (
            'from'  => '',
            'to'    => '',
        ),
        'banner_name'               => '',
        'visible_in'                => '',
        'banner_is_enabled'         => '',
        'related_banners'           => array (),
    );
    if (isset($data['simple_action']) && $data['simple_action'] == 'by_percent'
        && isset($data['discount_amount'])
    ) {
        $data['discount_amount'] = min(100,$data['discount_amount']);
    }
    if (isset($data['rule']['conditions'])) {
        $data['conditions'] = $data['rule']['conditions'];
    }
    if (isset($data['rule']['actions'])) {
        $data['actions'] = $data['rule']['actions'];
    }
    unset($data['rule']);

    $model->loadPost($data);
    $useAutoGeneration = (int)!empty($data['use_auto_generation']);
    $model->setUseAutoGeneration($useAutoGeneration);
    $model->save();
}
