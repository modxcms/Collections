<?php
namespace Collections\Processors\Selection;
use Collections\Model\CollectionSelection;
use Collections\Utils;
use MODX\Revolution\Processors\ModelProcessor;

/**
 * Delete multiple children
 *
 * @package collections
 * @subpackage processors.resource
 */
class RemoveMultiple extends ModelProcessor
{
    public $classKey = CollectionSelection::class;
    public $languageTopics = ['resource', 'collections:default'];

    public function process()
    {
        $collection = $this->getProperty('collection');
        $resources = $this->getProperty('resources', '');

        $resources = Utils::explodeAndClean($resources);

        if (empty($collection) || empty($resources)) {
            return $this->modx->lexicon('collections.err.selection_res_col_ns');
        }

        $this->modx->removeCollection($this->classKey, ['resource:IN' => $resources, 'collection' => $collection]);

        return $this->success();
    }
}
