<?php
/**
 * Resolve creating db tables
 *
 * @package containerx
 * @subpackage build
 */
if ($object->xpdo) {
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
            $modx =& $object->xpdo;
            $modelPath = $modx->getOption('containerx.core_path',null,$modx->getOption('core_path').'components/containerx/').'model/';
            $modx->addPackage('containerx',$modelPath);

            $manager = $modx->getManager();

            $manager->createObjectContainer('ContainerXItem');

            break;
        case xPDOTransport::ACTION_UPGRADE:
            break;
    }
}
return true;
