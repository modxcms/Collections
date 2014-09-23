<?php

namespace collections\events;


class OnBeforeDocFormSave extends CollectionsPlugin {

    public function run() {
        /** @var \modResource $resource */
        $resource = $this->scriptProperties['resource'];

        /** @var \modResource $parent */
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

        /** @var \modResource $original */
        $original = $this->modx->getObject('modResource', $resource->id);
        if ($original) {

            /** @var \modResource $originalParent */
            $originalParent = $original->Parent;
            if ($originalParent && (!$parent || ($originalParent->id != $parent->id))) {
                if ($originalParent->class_key == 'CollectionContainer') {
                    if ($parent->class_key != 'CollectionContainer') {
                        $resource->set('show_in_tree', 1);
                    }
                } else {
                    /** @var \modResource $originalGreatParent */
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
                    /** @var \modResource $child */
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
                    /** @var \modResource $child */
                    foreach ($children as $child) {
                        $child->set('show_in_tree', 1);
                        $child->save();
                    }
                }
            }
        }
    }
}