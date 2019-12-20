<?php
namespace Collections\Processors\Selection;
use Collections\Model\CollectionSelection;
use MODX\Revolution\Processors\ModelProcessor;

/**
 * Reorder items after drag and drop in grid
 *
 * @package collections
 * @subpackage processors
 */
class DDReorder extends ModelProcessor
{
    public $classKey = CollectionSelection::class;
    public $languageTopics = ['collections:default'];
    public $objectType = 'collections.selection';

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
            'resource:!=' => $idItem,
            'collection' => $parent,
            'menuindex:>=' => min($oldIndex, $newIndex),
            'menuindex:<=' => max($oldIndex, $newIndex),
        ]);

        $items->sortby('menuindex', 'ASC');

        $itemsCollection = $this->modx->getCollection($this->classKey, $items);

        if (min($oldIndex, $newIndex) == $newIndex) {
            foreach ($itemsCollection as $item) {
                $itemObject = $this->modx->getObject($this->classKey, ['resource' => $item->get('resource'), 'collection' => $parent]);
                $itemObject->set('menuindex', $itemObject->get('menuindex') + 1);
                $itemObject->save();
            }
        } else {
            foreach ($itemsCollection as $item) {
                $itemObject = $this->modx->getObject($this->classKey, ['resource' => $item->get('resource'), 'collection' => $parent]);
                $itemObject->set('menuindex', $itemObject->get('menuindex') - 1);
                $itemObject->save();
            }
        }

        $itemObject = $this->modx->getObject($this->classKey, ['resource' => $idItem, 'collection' => $parent]);
        $itemObject->set('menuindex', $newIndex);
        $itemObject->save();


        return $this->success('', $itemObject);
    }

}
