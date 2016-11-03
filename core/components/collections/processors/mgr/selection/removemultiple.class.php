<?php

/**
 * Delete multiple children
 *
 * @package collections
 * @subpackage processors.resource
 */
class CollectionSelectionRemoveMultipleProcessor extends modObjectProcessor
{
    public $classKey = 'CollectionSelection';
    public $languageTopics = array('resource', 'collections:default');

    public function process()
    {
        $collection = $this->getProperty('collection');
        $resources = $this->getProperty('resources', '');

        $resources = $this->modx->collections->explodeAndClean($resources);

        if (empty($collection) || empty($resources)) {
            return $this->modx->lexicon('collections.err.selection_res_col_ns');
        }

        $this->modx->removeCollection($this->classKey, array('resource:IN' => $resources, 'collection' => $collection));

        return $this->success();
    }
}

return 'CollectionSelectionRemoveMultipleProcessor';