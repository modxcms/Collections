<?php
/**
 * Update a Template column
 *
 * @package collections
 * @subpackage processors.template.column
 */
class CollectionsTemplateColumnUpdateProcessor extends modObjectUpdateProcessor {
    public $classKey = 'CollectionTemplateColumn';
    public $languageTopics = array('collections:default');
    public $objectType = 'collections.template.column';
    /** @var CollectionTemplate $object */
    public $object;

    public function beforeSet() {
        $template = (int) $this->getProperty('template');

        if ($template <= 0) return false;

        $name = $this->getProperty('name');

        if (empty($name)) {
            $this->addFieldError('name',$this->modx->lexicon('collections.err.column_ns_name'));
        } else {
            if (strpos($name, '.') !== false) {
                $this->addFieldError('name',$this->modx->lexicon('collections.err.column_dot_name'));
            }

            if ($this->modx->getCount($this->classKey, array('name' => $name, 'template' => $template, 'id:!=' => $this->object->id)) > 0) {
                $this->addFieldError('name',$this->modx->lexicon('collections.err.column_ae_name'));
            }
        }

        if ($this->object->name == 'id' && $name != 'id') {
            $this->addFieldError('name',$this->modx->lexicon('collections.err.column_name_cant_change'));
        }

        return parent::beforeSet();
    }

    public function afterSave() {

        return parent::afterSave();
    }

}
return 'CollectionsTemplateColumnUpdateProcessor';