<?php
/**
 * Remove a Template column.
 *
 * @package collections
 * @subpackage processors
 */
class CollectionTemplateColumnRemoveProcessor extends modObjectRemoveProcessor {
    public $classKey = 'CollectionTemplateColumn';
    public $languageTopics = array('collections:default');
    public $objectType = 'collections.template.column';

    public function beforeRemove() {

        if ($this->object->name == 'id') {
            return $this->modx->lexicon('collections.err.cant_remove_id_column');
        }

        return parent::beforeRemove();
    }
}
return 'CollectionTemplateColumnRemoveProcessor';