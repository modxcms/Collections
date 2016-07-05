<?php

/**
 * Reorder items after drag and drop in grid
 *
 * @package collections
 * @subpackage processors
 */
class CollectionsReorderResourceUpdateProcessor extends modObjectProcessor
{
    public $classKey = 'modResource';
    public $languageTopics = array('collections:default');
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
        $items->where(array(
            'id:!=' => $idItem,
            'parent' => $parent,
            'menuindex:>=' => min($oldIndex, $newIndex),
            'menuindex:<=' => max($oldIndex, $newIndex),
        ));

        $items->sortby('menuindex', 'ASC');

        $itemsCollection = $this->modx->getCollection($this->classKey, $items);

        if (min($oldIndex, $newIndex) == $newIndex) {
            foreach ($itemsCollection as $item) {
                $itemObject = $this->modx->getObject($this->classKey, $item->get('id'));
                $itemObject->set('menuindex', $itemObject->get('menuindex') + 1);
                $itemObject->save();
            }
        } else {
            foreach ($itemsCollection as $item) {
                $itemObject = $this->modx->getObject($this->classKey, $item->get('id'));
                $itemObject->set('menuindex', $itemObject->get('menuindex') - 1);
                $itemObject->save();
            }
        }

        $itemObject = $this->modx->getObject($this->classKey, $idItem);
        $itemObject->set('menuindex', $newIndex);
        $itemObject->save();


        return $this->success('', $itemObject);
    }

}

return 'CollectionsReorderResourceUpdateProcessor';