<?php

/**
 * Remove a Selection.
 *
 * @package collections
 * @subpackage processors
 */
class CollectionSelectionRemoveProcessor extends modObjectRemoveProcessor
{
    public $classKey = 'CollectionSelection';
    public $languageTopics = array('collections:default');
    public $objectType = 'collections.selection';

    public function initialize()
    {
        $collection = $this->getProperty('collection');
        $resource = $this->getProperty('resource');

        if (empty($collection) || empty($resource)) {
            return $this->modx->lexicon('collections.err.selection_res_col_ns');
        }

        $this->object = $this->modx->getObject($this->classKey, array('collection' => $collection, 'resource' => $resource));
        if (empty($this->object)) return $this->modx->lexicon($this->objectType . '_err_nfs', array($this->primaryKeyField => $collection . ',' . $resource));

        if ($this->checkRemovePermission && $this->object instanceof modAccessibleObject && !$this->object->checkPolicy('remove')) {
            return $this->modx->lexicon('access_denied');
        }

        return true;
    }

}

return 'CollectionSelectionRemoveProcessor';