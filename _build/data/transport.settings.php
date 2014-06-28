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
    'value' => '%b %d',
    'xtype' => 'textfield',
    'namespace' => 'collections',
));

$settings['collections.mgr_time_format'] = $modx->newObject('modSystemSetting');
$settings['collections.mgr_time_format']->set('key', 'collections.mgr_time_format');
$settings['collections.mgr_time_format']->fromArray(array(
    'value' => '%H:%M %p',
    'xtype' => 'textfield',
    'namespace' => 'collections',
));

$settings['mgr_tree_icon_collectioncontainer'] = $modx->newObject('modSystemSetting');
$settings['mgr_tree_icon_collectioncontainer']->set('key', 'mgr_tree_icon_collectioncontainer');
$settings['mgr_tree_icon_collectioncontainer']->fromArray(array(
    'value' => 'icon-collectioncontainer',
    'xtype' => 'textfield',
    'namespace' => 'collections',
));

return $settings;