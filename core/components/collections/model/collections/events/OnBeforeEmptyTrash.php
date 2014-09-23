<?php

namespace collections\events;


class OnBeforeEmptyTrash extends CollectionsPlugin {

    public function run() {
        $ids = $this->scriptProperties['ids'];

        foreach ($ids as $id) {
            /** @var \modResource $resource */
            $resource = $this->modx->getObject('modResource', $id);
            if ($resource) {
                /** @var \modResource $parent */
                $parent = $resource->Parent;
                if ($parent) {
                    /** @var \modResource $grandParent */
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
    }
}