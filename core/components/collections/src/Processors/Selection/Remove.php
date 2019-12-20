<?php
namespace Collections\Processors\Selection;
use Collections\Model\CollectionSelection;
use MODX\Revolution\modAccessibleObject;
use MODX\Revolution\Processors\Model\RemoveProcessor;

/**
 * Remove a Selection.
 *
 * @package collections
 * @subpackage processors
 */
class Remove extends RemoveProcessor
{
    public $classKey = CollectionSelection::class;
    public $languageTopics = ['collections:default'];
    public $objectType = 'collections.selection';

    public function initialize()
    {
        $collection = $this->getProperty('collection');
        $resource = $this->getProperty('resource');

        if (empty($collection) || empty($resource)) {
            return $this->modx->lexicon('collections.err.selection_res_col_ns');
        }

        $this->object = $this->modx->getObject($this->classKey, ['collection' => $collection, 'resource' => $resource]);
        if (empty($this->object)) return $this->modx->lexicon($this->objectType . '_err_nfs', [$this->primaryKeyField => $collection . ',' . $resource]);

        if ($this->checkRemovePermission && $this->object instanceof modAccessibleObject && !$this->object->checkPolicy('remove')) {
            return $this->modx->lexicon('access_denied');
        }

        return true;
    }

}
