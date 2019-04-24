<?php
namespace Collections\Events;

class OnBeforeDocFormSave extends Event
{

    public function run()
    {
        /** @var \modResource $resource */
        $resource = $this->scriptProperties['resource'];

        /** @var \modResource $parent */
        $parent = $resource->Parent;

        if ($parent && ($parent->class_key == 'CollectionContainer')) {
            $this->handleParent($resource);
        }

        if ($resource->class_key == 'CollectionContainer') {
            $resource->set('show_in_tree', 1);
        }

        /** @var \modResource $original */
        $original = $this->modx->getObject('modResource', $resource->id);
        if ($original) {
            if ($resource->class_key == 'SelectionContainer' && $original->class_key != 'SelectionContainer') {
                $this->revealChildrenInTree($resource);
            }

            if ($resource->class_key != 'SelectionContainer' && $original->class_key == 'SelectionContainer') {
                $linkedResources = $this->modx->getCount('CollectionSelection', array('collection' => $resource->id));
                if ($linkedResources > 0) {
                    $this->modx->event->_output = $this->modx->lexicon('collections.err.cant_switch_from_selection_linked');

                    return;
                }
                $resource->save();
            }

            $this->handleOriginal($original, $parent, $resource);
        }
    }

    /**
     * @param \modResource $resource
     */
    protected function handleParent($resource)
    {
        $resource->set('show_in_tree', 0);
    }

    protected function revealChildrenInTree($resource)
    {
        /** @var \modResource[] $children */
        $children = $resource->Children;

        foreach ($children as $child) {
            $child->set('show_in_tree', 1);
            $child->save();
        }
    }

    /**
     * @param \modResource $original
     * @param \modResource $parent
     * @param \modResource $resource
     */
    protected function handleOriginal($original, $parent, $resource)
    {
        /** @var \modResource $originalParent */
        $originalParent = $original->Parent;

        if ($originalParent && (!$parent || ($originalParent->id != $parent->id))) {
            $this->handleOriginalParent($parent, $resource, $originalParent);
        }

        if ($original->class_key != $resource->class_key) {
            $this->switchResourceType($original, $resource);

        }
    }

    /**
     * @param \modResource $parent
     * @param \modResource $resource
     * @param \modResource $originalParent
     */
    protected function handleOriginalParent($parent, $resource, $originalParent)
    {
        if ($originalParent->class_key == 'CollectionContainer') {
            if ($parent->class_key != 'CollectionContainer') {
                $resource->set('show_in_tree', 1);
            }
        } else {
            /** @var \modResource $originalGreatParent */
            $originalGreatParent = $originalParent->Parent;

            if ($originalGreatParent && ($originalGreatParent->class_key == 'CollectionContainer')) {
                $resource->set('show_in_tree', 1);
            }
        }
    }

    /**
     * @param \modResource $original
     * @param \modResource $resource
     */
    protected function switchResourceType($original, $resource)
    {
        if (($original->class_key != 'CollectionContainer') && ($resource->class_key == 'CollectionContainer')) {
            $this->switchToCollections($resource);
        }

        if (($original->class_key == 'CollectionContainer') && ($resource->class_key != 'CollectionContainer')) {
            $this->revealChildrenInTree($resource);
        }
    }

    /**
     * @param \modResource $resource
     */
    protected function switchToCollections($resource)
    {
        /** @var \modResource[] $children */
        $children = $resource->Children;

        foreach ($children as $child) {
            $child->set('show_in_tree', 0);

            if ($child->class_key == 'CollectionContainer') {
                $child->set('show_in_tree', 1);
            }

            $child->save();
        }
    }
}