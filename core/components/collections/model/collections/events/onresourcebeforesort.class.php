<?php
class OnResourceBeforeSort extends CollectionsPlugin {

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

            $skip = $this->handleDropToSelection($parent, $resource, $originalParent);
            if ($skip === true) {
                $this->scriptProperties['nodes'][$id]['parent'] = "1";
            }

            $this->handleParent($parent, $resource, $originalParent);

            if ($originalParent) {
                $this->handleOriginalParent($originalParent);
            }

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
                $hasChildren = ($resource->hasChildren() != 0);

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

    /**
     * @param \modResource $parent
     * @param \modResource $resource
     */
    protected function handleDropToSelection($parent, $resource) {
        if ($parent->class_key == 'CollectionContainer' && $this->modx->collections->isSelection($parent)) {
            $link = $this->modx->newObject('CollectionSelection');
            $link->set('resource', $resource->id);
            $link->set('collection', $parent->id);
            $link->save();

            return true;
        }

        return false;
    }
}