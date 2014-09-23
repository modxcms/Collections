<?php

namespace collections\events;


class OnResourceBeforeSort extends CollectionsPlugin {

    public function run() {
        /** @var \modResource[] $nodes */
        $nodes = $this->scriptProperties['nodes'];
        foreach ($nodes as $node) {
            $resource = $this->modx->getObject('modResource', $node['id']);
            if ($resource) {
                $hasChildren = ($resource->hasChildren() != 0);

                /** @var \modResource $originalParent */
                $originalParent = $resource->Parent;

                /** @var \modResource $parent */
                $parent = $this->modx->getObject('modResource', $node['parent']);

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
                        /** @var \modResource $greatParent */
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
                    /** @var \modResource $originalGreatParent */
                    $originalGreatParent = $originalParent->Parent;
                    if ($originalGreatParent && ($originalGreatParent->class_key == 'CollectionContainer')) {
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
    }
}