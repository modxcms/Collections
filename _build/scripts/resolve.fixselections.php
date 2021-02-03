<?php
/**
 * @var \Teleport\Transport\Transport $transport
 * @var array $object
 * @var array $options
 */

use Collections\Model\SelectionContainer;


switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_UPGRADE:
        /** @var modX $modx */
        $modx =& $transport->xpdo;

        $modx->updateCollection(\MODX\Revolution\modResource::class, ['hide_children_in_tree' => 1], ['class_key' => SelectionContainer::class]);

        break;
}

return true;
