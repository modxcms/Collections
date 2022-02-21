<?php
/**
 * @var \Teleport\Transport\Transport $transport
 * @var array $object
 * @var array $options
 */

use MODX\Revolution\modResource;

set_time_limit(0);

switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UPGRADE:
        $modx =& $transport->xpdo;

        if (isset($modx->packages['collections'])) {
            unset($modx->packages['collections']);
        }

        $modx->removeExtensionPackage('collections');

        $modx->updateCollection(modResource::class, ['class_key' => 'Collections\\Model\\CollectionContainer'], ['class_key' => 'CollectionContainer']);
        $modx->updateCollection(modResource::class, ['class_key' => 'Collections\\Model\\SelectionContainer'], ['class_key' => 'SelectionContainer']);

        break;
}

return true;
