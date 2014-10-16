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
            $c->where(array('class_key' => 'CollectionContainer'));

            /** @var modResource $collections[] */
            $collections = $modx->getCollection('modResource', $c);
            foreach ($collections as $collection) {
                $children = $collection->Children;
                foreach ($children as $child) {
                    $child->set('show_in_tree', 1);
                    $child->save();
                }

                $collection->set('class_key', 'modDocument');
                $collection->save();
            }

            $c = $modx->newQuery('modResource');
            $c->where(array('class_key' => 'SelectionContainer'));

            /** @var modResource[] $selections */
            $selections = $modx->getCollection('modResource', $c);
            foreach ($selections as $selection) {
                $selection->set('hide_children_in_tree', 0);
                $selection->set('class_key', 'modDocument');
                $selection->save();
            }

            break;
    }
}
return true;