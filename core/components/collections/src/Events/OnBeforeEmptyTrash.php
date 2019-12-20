<?php
namespace Collections\Events;

use Collections\Model\CollectionContainer;

class OnBeforeEmptyTrash extends Event
{

    public function run()
    {
        $ids = $this->scriptProperties['ids'];

        foreach ($ids as $id) {
            /** @var \modResource $resource */
            $resource = $this->modx->getObject('modResource', $id);
            if (!$resource) return;

            $this->modx->removeCollection('CollectionSelection', ['resource' => $resource->id]);

            /** @var \modResource $parent */
            $parent = $resource->Parent;
            if (!$parent) return;

            /** @var \modResource $grandParent */
            $grandParent = $parent->Parent;
            if (!$grandParent) return;

            if (($grandParent->class_key == CollectionContainer::class) && ($parent->class_key != CollectionContainer::class)) {
                $parent->set('show_in_tree', 0);
                $parent->save();
            }
        }
    }
}
