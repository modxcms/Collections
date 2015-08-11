<?php
class CollectionsOnResourceBeforeSort extends CollectionsPlugin {

    public function run() {
        /** @var \modResource[] $nodes */
        $nodes =& $this->scriptProperties['nodes'];

        foreach ($nodes as $id => $node) {
            /** @var \modResource $resource */
            $resource = $this->modx->getObject('modResource', $node['id']);
            if (!$resource) continue;

            /** @var \modResource $originalParent */
            $originalParent = $resource->Parent;

            /** @var \modResource $parent */
            $parent = $this->modx->getObject('modResource', $node['parent']);

            if (($parent && $originalParent && $parent->id == $originalParent->id) || (!$parent && !$originalParent)) {
                continue;
            }


            $this->handleParent($parent, $resource, $originalParent);

//            if ($originalParent) {
//                $this->handleOriginalParent($originalParent);
//            }

            if ($resource->class_key == 'CollectionContainer') {
                $resource->set('show_in_tree', 1);
            }

            $resource->save();

        }
    }

    /**
     * @param \modResource $parent
     * @param \modResource $resource
     * @param \modResource $originalParent
     */
    protected function handleParent($parent, $resource, $originalParent) {
        if ($parent) {
            if ($parent->class_key == 'CollectionContainer') {
                $resource->set('show_in_tree', 0);
            } else {
                if (($originalParent->class_key == 'CollectionContainer') && ($parent->class_key != 'CollectionContainer')) {
                    $resource->set('show_in_tree', 1);
                }
            }
        } else {
            if ($originalParent && ($originalParent->class_key == 'CollectionContainer')) {
                $resource->set('show_in_tree', 1);
            }
        }
    }

    /**
     * @param \modResource $originalParent
     */
    protected function handleOriginalParent($originalParent) {
        $originalGreatParent = $originalParent->Parent;

        if ($originalGreatParent && ($originalGreatParent->class_key == 'CollectionContainer') && ($originalParent->hasChildren() == 0)) {
            $originalParent->set('show_in_tree', 0);
            $originalParent->save();
        }
    }
}