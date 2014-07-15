<?php
/**
 * Loads system settings into build
 *
 * @package collections
 * @subpackage build
 */
$settings = array();

$settings['collections.mgr_date_format'] = $modx->newObject('modSystemSetting');
$settings['collections.mgr_date_format']->set('key', 'collections.mgr_date_format');
$settings['collections.mgr_date_format']->fromArray(array(
    'value' => 'M d',
    'xtype' => 'textfield',
    'namespace' => 'collections',
));

$settings['collections.mgr_time_format'] = $modx->newObject('modSystemSetting');
$settings['collections.mgr_time_format']->set('key', 'collections.mgr_time_format');
$settings['collections.mgr_time_format']->fromArray(array(
    'value' => 'g:i a',
    'xtype' => 'textfield',
    'namespace' => 'collections',
));

$settings['collections.mgr_datetime_format'] = $modx->newObject('modSystemSetting');
$settings['collections.mgr_datetime_format']->set('key', 'collections.mgr_datetime_format');
$settings['collections.mgr_datetime_format']->fromArray(array(
    'value' => 'M d, g:i a',
    'xtype' => 'textfield',
    'namespace' => 'collections',
));

$settings['collections.user_js'] = $modx->newObject('modSystemSetting');
$settings['collections.user_js']->set('key', 'collections.user_js');
$settings['collections.user_js']->fromArray(array(
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'collections',
));

$settings['collections.user_css'] = $modx->newObject('modSystemSetting');
$settings['collections.user_css']->set('key', 'collections.user_css');
$settings['collections.user_css']->fromArray(array(
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'collections',
));

$settings['mgr_tree_icon_collectioncontainer'] = $modx->newObject('modSystemSetting');
$settings['mgr_tree_icon_collectioncontainer']->set('key', 'mgr_tree_icon_collectioncontainer');
$settings['mgr_tree_icon_collectioncontainer']->fromArray(array(
    'value' => 'collectioncontainer',
    'xtype' => 'textfield',
    'namespace' => 'collections',
));


return $settings;