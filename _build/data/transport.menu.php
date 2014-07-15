<?php
/**
 * Adds modActions and modMenus into package
 *
 * @package collections
 * @subpackage build
 */

$menu = array();

$action = $modx->newObject('modAction');
$action->fromArray(array(
    'id' => 1,
    'namespace' => 'collections',
    'parent' => 0,
    'controller' => 'index',
    'haslayout' => true,
    'lang_topics' => 'collections:default',
    'assets' => '',
), '', true, true);

$menu[0]= $modx->newObject('modMenu');
$menu[0]->fromArray(array(
    'text' => 'collections.menu.collection_templates',
    'parent' => 'components',
    'description' => 'collections.menu.collection_templates_desc',
    'namespace' => 'collections',
), '', true, true);
$menu[0]->addOne($action);

return $menu;