<?php
/**
 * Change parent after dropping to the Resource tree
 *
 * @package collections
 * @subpackage processors.resource
 */
class CollectionsChangeParentProcessor extends modObjectUpdateProcessor {
    public $classKey = 'modResource';

    /** @var modResource $object */
    public $object;

    public function beforeSet() {
        $parent = $this->getProperty('parent', '');

        if ($parent == '') {
            return $this->modx->lexicon('collections.err.parent_ns');
        } else {
            $parent = explode('_', $parent);

            /** @var modContext $ctx */
            $ctx = $this->modx->getObject('modContext', $parent[0]);
            if ($ctx) {
                $this->setProperty('context_key', $ctx->key);
                $this->setProperty('parent', intval($parent[1]));
            } else {
                return $this->modx->lexicon('collections.err.common');
            }
        }

        return parent::beforeSet();
    }

    public function beforeSave() {
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
return 'CollectionsChangeParentProcessor';