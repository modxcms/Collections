<?php
$corePath = $modx->getOption('collections.core_path', null, $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/collections/');
/** @var Collections $collections */
$collections = $modx->getService(
    'collections',
    'Collections',
    $corePath . 'model/collections/',
    array(
        'core_path' => $corePath
    )
);

$eventName = $modx->event->name;
switch($eventName) {
    case 'OnBeforeDocFormSave':
        /** @var modResource $parent */
        $parent = $modx->getObject('modResource', $resource->parent);
        if ($parent) {
            if ($parent->class_key == 'CollectionsContainer') {
                $resource->set('show_in_tree', 0);
            } else {
                $resource->set('show_in_tree', 1);
                $parent->set('show_in_tree', 1);
                $parent->save();
            }
        } else {
            $resource->set('show_in_tree', 1);
        }

        break;
    case 'OnResourceSort':
        foreach ($nodes as $node) {
            /** @var modResource $resource */
            $resource = $modx->getObject('modResource', $node['id']);
            if ($resource) {
                $hasChildren = ($resource->hasChildren() != 0);

                /** @var modResource $parent */
                $parent = $resource->Parent;
                if ($parent) {
                    $parentIsCRC = ($parent->class_key == 'CollectionsContainer');

                    if ($parentIsCRC == true) {
                        if ($hasChildren == false) {
                            $resource->set('show_in_tree', 0);
                        } else {
                            $resource->set('show_in_tree', 1);
                        }
                    } else {
                        $resource->set('show_in_tree', 1);
                        $parent->set('show_in_tree', 1);
                    }

                    $parent->save();
                    $resource->save();

                }
            }

        }

        break;
}