<?php
namespace Collections\Events;

use Collections\Model\CollectionContainer;
use Collections\Model\CollectionSelection;
use Collections\Model\SelectionContainer;
use MODX\Revolution\modResource;

class OnBeforeDocFormSave extends Event
{

    public function run()
    {
        /** @var modResource $resource */
        $resource = $this->scriptProperties['resource'];

        /** @var modResource $parent */
        $parent = $resource->Parent;

        if ($parent && ($parent->class_key == CollectionContainer::class)) {
            $this->handleParent($resource);
        }

        if ($resource->class_key == CollectionContainer::class) {
            $resource->set('show_in_tree', 1);
        }

        /** @var modResource $original */
        $original = $this->modx->getObject(modResource::class, $resource->id);
        if ($original) {
            if ($resource->class_key == SelectionContainer::class && $original->class_key != SelectionContainer::class) {
                $this->revealChildrenInTree($resource);
            }

            if ($resource->class_key != SelectionContainer::class && $original->class_key == SelectionContainer::class) {
                $linkedResources = $this->modx->getCount(CollectionSelection::class, ['collection' => $resource->id]);
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
     * @param modResource $resource
     */
    protected function handleParent($resource)
    {
        $resource->set('show_in_tree', 0);
    }

    protected function revealChildrenInTree($resource)
    {
        /** @var modResource[] $children */
        $children = $resource->Children;

        foreach ($children as $child) {
            $child->set('show_in_tree', 1);
            $child->save();
        }
    }

    /**
     * @param modResource $original
     * @param modResource $parent
     * @param modResource $resource
     */
    protected function handleOriginal($original, $parent, $resource)
    {
        /** @var modResource $originalParent */
        $originalParent = $original->Parent;

        if ($originalParent && (!$parent || ($originalParent->id != $parent->id))) {
            $this->handleOriginalParent($parent, $resource, $originalParent);
        }

        if ($original->class_key != $resource->class_key) {
            $this->switchResourceType($original, $resource);

        }
    }

    /**
     * @param modResource $parent
     * @param modResource $resource
     * @param modResource $originalParent
     */
    protected function handleOriginalParent($parent, $resource, $originalParent)
    {
        if ($originalParent->class_key == CollectionContainer::class) {
            if ($parent->class_key != CollectionContainer::class) {
                $resource->set('show_in_tree', 1);
            }
        } else {
            /** @var modResource $originalGreatParent */
            $originalGreatParent = $originalParent->Parent;

            if ($originalGreatParent && ($originalGreatParent->class_key == CollectionContainer::class)) {
                $resource->set('show_in_tree', 1);
            }
        }
    }

    /**
     * @param modResource $original
     * @param modResource $resource
     */
    protected function switchResourceType($original, $resource)
    {
        if (($original->class_key != CollectionContainer::class) && ($resource->class_key == CollectionContainer::class)) {
            $this->switchToCollections($resource);
        }

        if (($original->class_key == CollectionContainer::class) && ($resource->class_key != CollectionContainer::class)) {
            $this->revealChildrenInTree($resource);
        }
    }

    /**
     * @param modResource $resource
     */
    protected function switchToCollections($resource)
    {
        /** @var modResource[] $children */
        $children = $resource->Children;

        foreach ($children as $child) {
            $child->set('show_in_tree', 0);

            if ($child->class_key == CollectionContainer::class) {
                $child->set('show_in_tree', 1);
            }

            $child->save();
        }
    }
}
