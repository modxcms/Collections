<?php

/**
 * Change child's parent
 *
 * @package collections
 * @subpackage processors.resource
 */
class CollectionsChangeChildParentProcessor extends modObjectUpdateProcessor
{
    public $classKey = 'modResource';

    /** @var modResource $object */
    public $object;

    public function beforeSet()
    {
        $parent = $this->getProperty('parent', '');

        if ($parent == '') {
            return $this->modx->lexicon('collections.err.parent_ns');
        }

        return parent::beforeSet();
    }

    public function beforeSave()
    {
        /** @var modResource $parent */
        $parent = $this->modx->getObject('modResource', $this->object->parent);
        if ($parent && ($parent->class_key == 'CollectionContainer')) {
            $this->object->set('show_in_tree', 0);
        } else {
            $this->object->set('show_in_tree', 1);
        }

        return parent::beforeSave();
    }

}

return 'CollectionsChangeChildParentProcessor';
