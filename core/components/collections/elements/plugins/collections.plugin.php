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
    case 'OnManagerPageInit':
        $cssFile = $collections->getOption('assets_url').'css/mgr.css';
        $modx->regClientCSS($cssFile);
        break;
    case 'OnDocFormPrerender':
        $inject = false;

        /** @var modResource $parent */
        $parent = $resource->Parent;
        if (!$parent) {
            if (isset($_GET['parent'])) {
                $parent = intval($_GET['parent']);

                $parent = $modx->getObject('modResource', $parent);
                if ($parent){
                    $inject = ($parent->class_key == 'CollectionContainer');
                }
            }
        } else {
            $inject = ($parent->class_key == 'CollectionContainer');
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
            if ($parent->class_key == 'CollectionContainer') {
                $hasChildren = ($resource->hasChildren() != 0);
                if ($hasChildren) {
                    $resource->set('show_in_tree', 1);
                } else {
                    $resource->set('show_in_tree', 0);
                }
            }
        }

        if ($resource->class_key == 'CollectionContainer') {
            $resource->set('show_in_tree', 1);
        }

        /** @var modResource $original */
        $original = $modx->getObject('modResource', $resource->id);
        if ($original) {

            /** @var modResource $originalParent */
            $originalParent = $original->Parent;
            if ($originalParent && (!$parent || ($originalParent->id != $parent->id))) {
                if ($originalParent->class_key == 'CollectionContainer') {
                    if ($parent->class_key != 'CollectionContainer') {
                        $resource->set('show_in_tree', 1);
                    }
                } else {
                    /** @var modResource $originalGreatParent */
                    $originalGreatParent = $originalParent->Parent;
                    if ($originalGreatParent && ($originalGreatParent->class_key == 'CollectionContainer')) {
                        $resource->set('show_in_tree', 1);

                        $originalParentHasOtherChildren = ($originalParent->hasChildren() > 1);
                        if (!$originalParentHasOtherChildren) {
                            $originalParent->set('show_in_tree', 0);
                            $originalParent->save();
                        }
                    }
                }
            }

            // Switch Resource type
            if ($original->class_key != $resource->class_key) {
                // Switch to CollectionContainer
                if (($original->class_key != 'CollectionContainer') && ($resource->class_key == 'CollectionContainer')) {
                    $children = $resource->Children;
                    /** @var modResource $child */
                    foreach ($children as $child) {
                        $child->set('show_in_tree', 0);

                        if ($child->class_key == 'CollectionContainer') {
                            $child->set('show_in_tree', 1);
                        }

                        if ($child->hasChildren() > 0) {
                            $child->set('show_in_tree', 1);
                        }

                        $child->save();
                    }
                }

                // Switch from CollectionContainer
                if (($original->class_key == 'CollectionContainer') && ($resource->class_key != 'CollectionContainer')) {
                    $children = $resource->Children;
                    /** @var modResource $child */
                    foreach ($children as $child) {
                        $child->set('show_in_tree', 1);
                        $child->save();
                    }
                }
            }
        }

        break;

    case 'OnResourceBeforeSort':
        foreach ($nodes as $node) {
            /** @var modResource $resource */
            $resource = $modx->getObject('modResource', $node['id']);
            if ($resource) {
                $hasChildren = ($resource->hasChildren() != 0);

                /** @var modResource $originalParent */
                $originalParent = $resource->Parent;

                /** @var modResource $parent */
                $parent = $modx->getObject('modResource', $node['parent']);

                if (($parent && $originalParent && $parent->id == $originalParent->id) || (!$parent && !$originalParent)) {
                    continue;
                }

                if ($parent) {
                    if ($parent->class_key == 'CollectionContainer') {
                        if ($hasChildren == false) {
                            $resource->set('show_in_tree', 0);
                        } else {
                            $resource->set('show_in_tree', 1);
                        }
                    } else {
                        /** @var modResource $greatParent */
                        $greatParent = $parent->Parent;
                        if ($greatParent && ($greatParent->class_key == 'CollectionContainer')) {
                            $parent->set('show_in_tree', 1);
                            $parent->save();
                        }

                        if (($originalParent->class_key == 'CollectionContainer') && ($parent->class_key != 'CollectionContainer')) {
                            $resource->set('show_in_tree', 1);
                        }

                    }
                } else {
                    if ($originalParent && ($originalParent->class_key == 'CollectionContainer')) {
                        $resource->set('show_in_tree', 1);
                    }
                }

                if ($originalParent) {
                    /** @var modResource $originalGreatParent */
                    $originalGreatParent = $originalParent->Parent;
                    if ($originalGreatParent && ($originalGreatParent->class_key = 'CollectionContainer')) {
                        $originalParentHasOtherChildren = ($originalParent->hasChildren() > 1);
                        if (!$originalParentHasOtherChildren) {
                            $originalParent->set('show_in_tree', 0);
                            $originalParent->save();
                        }
                    }
                }

                if ($resource->class_key == 'CollectionContainer') {
                    $resource->set('show_in_tree', 1);
                }

                $resource->save();
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
                        if ($grandParent->class_key == 'CollectionContainer') {
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