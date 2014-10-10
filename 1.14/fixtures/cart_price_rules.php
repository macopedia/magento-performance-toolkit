<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** @var \Magento\ToolkitFramework\Application $this */
$cartPriceRulesCount = \Magento\ToolkitFramework\Config::getInstance()->getValue('cart_price_rules', 10);
$cartPriceRulesProductsFloor = \Magento\ToolkitFramework\Config::getInstance()->getValue('cart_price_rules_floor', 2);

$model =  Mage::getModel('salesrule/rule');
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

for ($i = 0; $i < $cartPriceRulesCount; $i++) {
    $ruleName = sprintf('Shopping Cart Price Rule %1$d', $i);
    $data = array(
        $idField                => null,
        'product_ids'           => '',
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
        'coupon_type'           => '1',
        'coupon_code'           => '',
        'uses_per_customer'     => '',
        'from_date'             => '',
        'to_date'               => '',
        'sort_order'            => '',
        'is_rss'                => '1',
        'rule'                  => array (
            'conditions' =>
            array (
                1 =>
                array (
                    'type' => 'salesrule/rule_condition_combine',
                    'aggregator' => 'all',
                    'value' => '1',
                    'new_child' => '',
                ),
                '1--1' =>
                array (
                    'type' => 'salesrule/rule_condition_address',
                    'attribute' => 'total_qty',
                    'operator' => '>=',
                    'value' => $cartPriceRulesProductsFloor + $i,
                ),
                '1--2' =>
                array (
                    'type' => 'salesrule/rule_condition_product_found',
                    'value' => '1',
                    'aggregator' => 'all',
                    'new_child' => '',
                ),
                '1--2--1' =>
                array (
                    'type' => 'salesrule/rule_condition_product',
                    'attribute' => 'category_ids',
                    'operator' => '==',
                    'value' => $categories_array[$i % count($categories_array)][0],
                ),
            ),
            'actions' =>
            array (
                1 =>
                array (
                    'type' => 'salesrule/rule_condition_product_combine',
                    'aggregator' => 'all',
                    'value' => '1',
                    'new_child' => '',
                ),
            ),
        ),
        'simple_action'             => 'by_percent',
        'discount_amount'           => '10',
        'discount_qty'              => '0',
        'discount_step'             => '',
        'apply_to_shipping'         => '0',
        'simple_free_shipping'      => '0',
        'stop_rules_processing'     => '0',
        'reward_points_delta'       => '',
        'store_labels'              => array (
            0 => '',
            1 => '',
            2 => '',
            3 => '',
            4 => '',
            5 => '',
            6 => '',
            7 => '',
            8 => '',
            9 => '',
            10 => '',
            11 => '',
        ),
        'page'                      => '1',
        'limit'                     => '20',
        'in_banners'                => '',
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
