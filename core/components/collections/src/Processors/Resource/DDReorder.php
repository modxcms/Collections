<?php
namespace Collections\Processors\Resource;
use MODX\Revolution\modResource;
use MODX\Revolution\Processors\ModelProcessor;

class DDReorder extends ModelProcessor
{
    public $classKey = modResource::class;
    public $languageTopics = ['collections:default'];
    public $objectType = 'collections.children';

    public function process()
    {
        $idItem = $this->getProperty('idItem');
        $oldIndex = $this->getProperty('oldIndex');
        $newIndex = $this->getProperty('newIndex');
        $parent = $this->getProperty('parent');

        if (intval($parent) == 0) {
            return $this->failure($this->modx->lexicon('collections.err.parent_ns'));
        }

        $items = $this->modx->newQuery($this->classKey);
        $items->where([
            'id:!=' => $idItem,
            'parent' => $parent,
            'menuindex:>=' => min($oldIndex, $newIndex),
            'menuindex:<=' => max($oldIndex, $newIndex),
        ]);

        $items->sortby('menuindex', 'ASC');

        $itemsCollection = $this->modx->getCollection($this->classKey, $items);
        $affectedResources = [];

        if (min($oldIndex, $newIndex) == $newIndex) {
            foreach ($itemsCollection as $item) {
                $itemObject = $this->modx->getObject($this->classKey, $item->get('id'));
                $itemObject->set('menuindex', $itemObject->get('menuindex') + 1);
                $itemObject->save();

                $affectedResources[] = $itemObject;
            }
        } else {
            foreach ($itemsCollection as $item) {
                $itemObject = $this->modx->getObject($this->classKey, $item->get('id'));
                $itemObject->set('menuindex', $itemObject->get('menuindex') - 1);
                $itemObject->save();

                $affectedResources[] = $itemObject;
            }
        }

        $itemObject = $this->modx->getObject($this->classKey, $idItem);
        $itemObject->set('menuindex', $newIndex);
        $itemObject->save();

        $this->modx->invokeEvent('CollectionsOnResourceSort', [
            'resourceId' => $idItem,
            'parentId' => $parent,
            'oldIndex' => $oldIndex,
            'newIndex' => $newIndex,
            'affectedResources' => &$affectedResources,
        ]);

        return $this->success('', $itemObject);
    }
}
