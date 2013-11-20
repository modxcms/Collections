<?php
/**
 * Collections
 *
 * DESCRIPTION
 *
 * This plugin inject JS to handle proper working of close buttons in Resource's panel (OnDocFormPrerender)
 * This plugin handles setting proper show_in_tree parameter (OnBeforeDocFormSave, OnResourceSort)
 *
 */

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
    case 'OnDocFormPrerender':
        $inject = false;

        /** @var modResource $parent */
        $parent = $resource->Parent;
        if (!$parent) {
            if (isset($_GET['parent'])) {
                $parent = intval($_GET['parent']);

                $parent = $modx->getObject('modResource', $parent);
                if ($parent){
                    $inject = ($parent->class_key == 'CollectionsContainer');
                }
            }
        } else {
            $inject = ($parent->class_key == 'CollectionsContainer');
        }

        if ($inject) {
            $jsurl = $collections->config['jsUrl'].'mgr/';
            $modx->regClientStartupScript($jsurl.'extra/hijackclose.js');
        }

        break;

    case 'OnBeforeDocFormSave':
        /** @var modResource $parent */
        $parent = $resource->Parent;
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

        if ($resource->class_key == 'CollectionsContainer') {
            $resource->set('show_in_tree', 1);
        } else {
            $hasChildren = ($resource->hasChildren() != 0);
            if ($hasChildren) {
                $resource->set('show_in_tree', 1);
            }
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

                    if ($resource->class_key == 'CollectionsContainer') {
                        $resource->set('show_in_tree', 1);
                    }

                    $parent->save();
                    $resource->save();

                }
            }

        }

        break;

    case 'OnBeforeEmptyTrash':
        foreach ($ids as $id) {
            /** @var modResource $resource */
            $resource = $modx->getObject('modResource', $id);
            if ($resource) {
                /** @var modResource $parent */
                $parent = $resource->Parent;
                if ($parent) {
                    /** @var modResource $grandParent */
                    $grandParent = $parent->Parent;
                    if ($grandParent) {
                        if ($grandParent->class_key == 'CollectionsContainer') {
                            $parentHasOtherChildren = ($parent->hasChildren() > 1);
                            if ($parentHasOtherChildren == false) {
                                $parent->set('show_in_tree', 0);
                                $parent->save();
                            }
                        }
                    }
                }
            }
        }
        break;
}