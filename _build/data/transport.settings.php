<?php
/**
 * Loads system settings into build
 *
 * @package collections
 * @subpackage build
 */
$settings = array();

$settings['collections.mgr_date_format'] = $modx->newObject('modSystemSetting');
$settings['collections.mgr_date_format']->fromArray(array(
	'key'		=> 'collections.mgr_date_format',
    'value'		=> '%b %d',
    'xtype'		=> 'textfield',
    'namespace'	=> 'collections',
    'area'		=> ''
));

$settings['collections.mgr_time_format'] = $modx->newObject('modSystemSetting');
$settings['collections.mgr_time_format']->fromArray(array(
	'key'		=> 'collections.mgr_time_format',
    'value'		=> '%H:%M %p',
    'xtype'		=> 'textfield',
    'namespace'	=> 'collections',
    'area'		=> ''
));

$settings['collections.mgr_default_sort_field'] = $modx->newObject('modSystemSetting');
$settings['collections.mgr_default_sort_field']->fromArray(array(
	'key'		=> 'collections.mgr_default_sort_field',
    'value'		=> 'createdon',
    'xtype'		=> 'textfield',
    'namespace'	=> 'collections',
    'area'		=> ''
));

$settings['collections.mgr_default_sort_dir'] = $modx->newObject('modSystemSetting');
$settings['collections.mgr_default_sort_dir']->fromArray(array(
	'key'		=> 'collections.mgr_default_sort_dir',
    'value'		=> 'DESC',
    'xtype'		=> 'textfield',
    'namespace'	=> 'collections',
    'area'		=> ''
));

return $settings;