<?php
namespace Collections\Events;

class OnBeforeEmptyTrash extends Event
{

    public function run()
    {
        $ids = $this->scriptProperties['ids'];

        foreach ($ids as $id) {
            /** @var \modResource $resource */
            $resource = $this->modx->getObject('modResource', $id);
            if (!$resource) return;

            $this->modx->removeCollection('CollectionSelection', array('resource' => $resource->id));

            /** @var \modResource $parent */
            $parent = $resource->Parent;
            if (!$parent) return;

            /** @var \modResource $grandParent */
            $grandParent = $parent->Parent;
            if (!$grandParent) return;

            if (($grandParent->class_key == 'CollectionContainer') && ($parent->class_key != 'CollectionContainer')) {
                $parent->set('show_in_tree', 0);
                $parent->save();
            }
        }
    }
}