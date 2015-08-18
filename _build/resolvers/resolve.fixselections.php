<?php
if ($object->xpdo) {
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_UPGRADE:
            /** @var modX $modx */
            $modx =& $object->xpdo;

            $modx->updateCollection('modResource', array('hide_children_in_tree' => 1), array('class_key' => 'SelectionContainer'));

            break;
    }
}
return true;