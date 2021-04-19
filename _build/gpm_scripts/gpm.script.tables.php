<?php
use xPDO\Transport\xPDOTransport;

/**
 * Create tables
 *
 * THIS SCRIPT IS AUTOMATICALLY GENERATED, NO CHANGES WILL APPLY
 *
 * @package collections
 * @subpackage build.scripts
 *
 * @var \xPDO\Transport\xPDOTransport $transport
 * @var array $object
 * @var array $options
 */

$modx =& $transport->xpdo;

if ($options[xPDOTransport::PACKAGE_ACTION] === xPDOTransport::ACTION_UNINSTALL) return true;

$manager = $modx->getManager();

$manager->createObjectContainer(\Collections\Model\CollectionSetting::class);
$manager->createObjectContainer(\Collections\Model\CollectionTemplate::class);
$manager->createObjectContainer(\Collections\Model\CollectionTemplateColumn::class);
$manager->createObjectContainer(\Collections\Model\CollectionResourceTemplate::class);
$manager->createObjectContainer(\Collections\Model\CollectionSelection::class);

return true;
