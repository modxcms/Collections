<?php
if ($object->xpdo) {
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:


        case xPDOTransport::ACTION_UPGRADE:
            /** @var modX $modx */
            $modx =& $object->xpdo;

            $modelPath = $modx->getOption('collections.core_path',null,$modx->getOption('core_path').'components/collections/').'model/';
            $modx->addPackage('collections',$modelPath);

            $manager = $modx->getManager();
            $manager->createObjectContainer('CollectionSetting');
            $manager->createObjectContainer('CollectionTemplate');
            $manager->createObjectContainer('CollectionTemplateColumn');
            $manager->createObjectContainer('CollectionResourceTemplate');

            break;
    }
}
return true;