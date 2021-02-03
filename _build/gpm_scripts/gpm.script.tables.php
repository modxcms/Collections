<?php
/**
 * Create tables
 *
 * THIS SCRIPT IS AUTOMATICALLY GENERATED, NO CHANGES WILL APPLY
 *
 * @package collections
 * @subpackage build.scripts
 *
 * @var \Teleport\Transport\Transport $transport
 * @var array $object
 * @var array $options
 */

$modx =& $transport->xpdo;

if ($options[xPDOTransport::PACKAGE_ACTION] === xPDOTransport::ACTION_UNINSTALL) return true;

$manager = $modx->getManager();

$manager->createObjectContainer('\Collections\Model\CollectionSetting');
$manager->createObjectContainer('\Collections\Model\CollectionTemplate');
$manager->createObjectContainer('\Collections\Model\CollectionTemplateColumn');
$manager->createObjectContainer('\Collections\Model\CollectionResourceTemplate');
$manager->createObjectContainer('\Collections\Model\CollectionSelection');

return true;
