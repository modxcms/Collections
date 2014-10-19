<?php
/**
 * Create a Template column
 *
 * @package collections
 * @subpackage processors.template.column
 */
class CollectionsTemplateColumnCreateProcessor extends modObjectCreateProcessor {
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

            if ($this->doesAlreadyExist(array('name' => $name, 'template' => $template))) {
                $this->addFieldError('name',$this->modx->lexicon('collections.err.column_ae_name'));
            }
        }

        $position = $this->getProperty('position');
        if (empty($position)) {
            $c = $this->modx->newQuery('CollectionTemplateColumn');
            $c->where(array(
                'template' => $template
            ));
            $c->limit(1);
            $c->sortby('position', 'DESC');

            $last = 0;

            $columns = $this->modx->getIterator('CollectionTemplateColumn', $c);
            foreach ($columns as $column) {
                $last = $column->position + 1;
                break;
            }

            $this->setProperty('position', $last);
        }

        $width = (int) $this->getProperty('width');
        if ($width <= 0) {
            $this->setProperty('width', 100);
        }

        return parent::beforeSet();
    }

    public function afterSave() {

        return parent::afterSave();
    }

}
return 'CollectionsTemplateColumnCreateProcessor';