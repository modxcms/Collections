<?php
/**
 * Resolve creating db tables
 *
 * THIS RESOLVER IS AUTOMATICALLY GENERATED, NO CHANGES WILL APPLY
 *
 * @package collections
 * @subpackage build
 *
 * @var mixed $object
 * @var modX $modx
 * @var array $options
 */

if ($object->xpdo) {
    $modx =& $object->xpdo;
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            $modelPath = $modx->getOption('collections.core_path', null, $modx->getOption('core_path') . 'components/collections/') . 'model/';
            
            $modx->addPackage('collections', $modelPath, null);


            $manager = $modx->getManager();

            $manager->createObjectContainer('CollectionSetting');
            $manager->createObjectContainer('CollectionTemplate');
            $manager->createObjectContainer('CollectionTemplateColumn');
            $manager->createObjectContainer('CollectionResourceTemplate');
            $manager->createObjectContainer('CollectionSelection');

            break;
    }
}

return true;