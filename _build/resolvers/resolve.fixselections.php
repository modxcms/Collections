<?php

use Collections\Model\SelectionContainer;

if ($object->xpdo) {
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_UPGRADE:
            /** @var modX $modx */
            $modx =& $object->xpdo;

            $modx->updateCollection(\MODX\Revolution\modResource::class, ['hide_children_in_tree' => 1], ['class_key' => SelectionContainer::class]);

            break;
    }
}
return true;
