<?php
namespace Collections\Processors\Resource;
use Collections\Model\CollectionContainer;
use MODX\Revolution\modContext;
use MODX\Revolution\modResource;
use MODX\Revolution\Processors\Model\UpdateProcessor;

class ChangeParent extends UpdateProcessor
{
    public $classKey = modResource::class;

    /** @var modResource $object */
    public $object;

    public function beforeSet()
    {
        $parent = $this->getProperty('parent', '');

        if ($parent == '') {
            return $this->modx->lexicon('collections.err.parent_ns');
        } else {
            $parent = explode('_', $parent);

            /** @var modContext $ctx */
            $ctx = $this->modx->getObject(modContext::class, $parent[0]);
            if ($ctx) {
                $this->setProperty('context_key', $ctx->key);
                $this->setProperty('parent', intval($parent[1]));
            } else {
                return $this->modx->lexicon('collections.err.common');
            }
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
