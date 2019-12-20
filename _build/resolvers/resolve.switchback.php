<?php

use Collections\Model\CollectionContainer;
use Collections\Model\SelectionContainer;
use MODX\Revolution\modDocument;

if ($object->xpdo) {
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            break;
        case xPDOTransport::ACTION_UNINSTALL:
            /** @var modX $modx */
            $modx =& $object->xpdo;

            $c = $modx->newQuery(\MODX\Revolution\modResource::class);
            $c->where(['class_key' => CollectionContainer::class]);

            /** @var modResource $collections[] */
            $collections = $modx->getCollection(\MODX\Revolution\modResource::class, $c);
            foreach ($collections as $collection) {
                $children = $collection->Children;
                foreach ($children as $child) {
                    $child->set('show_in_tree', 1);
                    $child->save();
                }

                $collection->set('class_key', modDocument::class);
                $collection->save();
            }

            $c = $modx->newQuery(\MODX\Revolution\modResource::class);
            $c->where(['class_key' => SelectionContainer::class]);

            /** @var modResource[] $selections */
            $selections = $modx->getCollection(\MODX\Revolution\modResource::class, $c);
            foreach ($selections as $selection) {
                $selection->set('hide_children_in_tree', 0);
                $selection->set('class_key', modDocument::class);
                $selection->save();
            }

            break;
    }
}
return true;
