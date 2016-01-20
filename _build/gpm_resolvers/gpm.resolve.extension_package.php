<?php
/**
 * Resolve creating db tables
 *
 * THIS RESOLVER IS AUTOMATICALLY GENERATED, NO CHANGES WILL APPLY
 *
 * @package collections
 * @subpackage build
 */

if ($object->xpdo) {
    /** @var modX $modx */
    $modx =& $object->xpdo;

    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            $modelPath = $modx->getOption('collections.core_path');

            if (empty($modelPath)) {
                $modelPath = '[[++core_path]]components/collections/model/';
            }

            if ($modx instanceof modX) {

                $modx->addExtensionPackage('collections', $modelPath, array (
));

            }

            break;
        case xPDOTransport::ACTION_UNINSTALL:
            if ($modx instanceof modX) {
                $modx->removeExtensionPackage('collections');
            }

            break;
    }
}
return true;