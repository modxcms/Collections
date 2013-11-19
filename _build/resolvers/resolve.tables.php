<?php
/**
 * Resolve creating db tables
 *
 * @package collections
 * @subpackage build
 */
if ($object->xpdo) {
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
            $modx =& $object->xpdo;
            $modelPath = $modx->getOption('collections.core_path',null,$modx->getOption('core_path').'components/collections/').'model/';
            $modx->addPackage('collections',$modelPath);

            $manager = $modx->getManager();

            $manager->createObjectContainer('CollectionsItem');

            break;
        case xPDOTransport::ACTION_UPGRADE:
            break;
    }
}
return true;
