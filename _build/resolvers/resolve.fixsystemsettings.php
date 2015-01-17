<?php
if ($object->xpdo) {
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_UPGRADE:
            /** @var modX $modx */
            $modx =& $object->xpdo;

            /** @var modSystemSetting $ss */
            $ss = $modx->getObject('modSystemSetting', array('key' => 'renderer_image_path'));
            if ($ss) {
                $ss->remove();
            }

            break;
    }
}
return true;