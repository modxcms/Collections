<?php
/**
 * Define the MODX path constants necessary for installation
 *
 * @package collections
 * @subpackage build
 */
define('MODX_BASE_PATH', dirname(dirname(dirname(dirname(__FILE__)))) . '/modx/');
define('MODX_CORE_PATH', MODX_BASE_PATH . 'core/');
define('MODX_PACKAGES_PATH',dirname(dirname(__FILE__)).'/_packages/');
define('MODX_MANAGER_PATH', MODX_BASE_PATH . 'manager/');
define('MODX_CONNECTORS_PATH', MODX_BASE_PATH . 'connectors/');
define('MODX_ASSETS_PATH', MODX_BASE_PATH . 'assets/');

define('MODX_BASE_URL','/modx/');
define('MODX_CORE_URL', MODX_BASE_URL . 'core/');
define('MODX_MANAGER_URL', MODX_BASE_URL . 'manager/');
define('MODX_CONNECTORS_URL', MODX_BASE_URL . 'connectors/');
define('MODX_ASSETS_URL', MODX_BASE_URL . 'assets/');