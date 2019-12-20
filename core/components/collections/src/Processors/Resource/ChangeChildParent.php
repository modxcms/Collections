<?php
namespace Collections\Processors\Resource;
use Collections\Model\CollectionContainer;
use MODX\Revolution\modResource;
use MODX\Revolution\Processors\Model\UpdateProcessor;

class ChangeChildParent extends UpdateProcessor
{
    public $classKey = modResource::class;

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
        $parent = $this->modx->getObject(modResource::class, $this->object->parent);
        if ($parent && ($parent->class_key == CollectionContainer::class)) {
            $this->object->set('show_in_tree', 0);
        } else {
            $this->object->set('show_in_tree', 1);
        }

        return parent::beforeSave();
    }

}
