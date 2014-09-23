<?php

namespace collections\events;

class OnBeforeEmptyTrash extends CollectionsPlugin {

    public function run() {
        $ids = $this->scriptProperties['ids'];

        foreach ($ids as $id) {
            /** @var \modResource $resource */
            $resource = $this->modx->getObject('modResource', $id);
            if (!$resource) return;

            /** @var \modResource $parent */
            $parent = $resource->Parent;
            if (!$parent) return;

            /** @var \modResource $grandParent */
            $grandParent = $parent->Parent;
            if (!$grandParent) return;

            if ($grandParent->class_key == 'CollectionContainer' && ($parent->hasChildren() == 0)) {
                $parent->set('show_in_tree', 0);
                $parent->save();
            }
        }
    }
}