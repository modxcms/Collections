<?php
/**
 * @var \Teleport\Transport\Transport $transport
 * @var array $object
 * @var array $options
 */

use Collections\Model\CollectionContainer;
use Collections\Model\SelectionContainer;
use MODX\Revolution\modResource;

set_time_limit(0);

switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UPGRADE:
        $modx =& $transport->xpdo;

        $modx->removeExtensionPackage('collections');

        $oldCollections = $modx->getIterator(modResource::class, ['class_key' => 'CollectionContainer']);
        foreach ($oldCollections as $oldCollection) {
            $oldCollection->set('class_key', CollectionContainer::class);
            $oldCollection->save();
        }

        $oldSelections = $modx->getIterator(modResource::class, ['class_key' => 'SelectionContainer']);
        foreach ($oldSelections as $oldSelection) {
            $oldSelection->set('class_key', SelectionContainer::class);
            $oldSelection->save();
        }

        break;
}

return true;
