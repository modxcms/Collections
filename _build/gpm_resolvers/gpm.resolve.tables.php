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
            $srcPath = $modx->getOption('collections.core_path', null, $modx->getOption('core_path') . 'components/collections/') . 'src/';
            $modx->addPackage('Collections\Model', $srcPath, null, 'Collections\\');

            $manager = $modx->getManager();

            $manager->createObjectContainer(\Collections\Model\CollectionSetting::class);
            $manager->createObjectContainer(\Collections\Model\CollectionTemplate::class);
            $manager->createObjectContainer(\Collections\Model\CollectionTemplateColumn::class);
            $manager->createObjectContainer(\Collections\Model\CollectionResourceTemplate::class);
            $manager->createObjectContainer(\Collections\Model\CollectionSelection::class);

            break;
    }
}

return true;
