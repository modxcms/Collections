<?php

if ($object->xpdo) {
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            break;
        case xPDOTransport::ACTION_UNINSTALL:
            /** @var modX $modx */
            $modx =& $object->xpdo;

            $c = $modx->newQuery('modResource');
            $c->where(array('class_key' => 'CollectionsContainer'));

            $collections = $modx->getCollection('modResource', $c);
            /** @var modResource $collection */
            foreach ($collections as $collection) {
                $children = $collection->Children;
                foreach ($children as $child) {
                    $child->set('show_in_tree', 1);
                    $child->save();
                }

                $collection->set('class_key', 'modDocument');
                $collection->save();
            }

            break;
    }
}
return true;