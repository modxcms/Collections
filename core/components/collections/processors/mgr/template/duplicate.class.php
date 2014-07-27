<?php
/**
 * Duplicate a Template
 *
 * @package collections
 * @subpackage processors.template
 */
class CollectionsTemplateDuplicateProcessor extends modProcessor {
    /** @var CollectionTemplate $oldTemplate */
    public $oldTemplate;
    public $classKey = 'CollectionTemplate';

    public function getLanguageTopics() {
        return array('collections:default');
    }

    public function initialize() {
        $id = $this->getProperty('id', false);
        if (empty($id)) return $this->modx->lexicon('collections.err.template_ns');

        $this->oldTemplate = $this->modx->getObject($this->classKey, $id);
        if (empty($this->oldTemplate)) return $this->modx->lexicon('collections.err.template_ns');

        return true;
    }

    public function process() {
        $canDuplicate = $this->beforeDuplicate();
        if ($canDuplicate !== true) {
            return $this->failure($canDuplicate);
        }

        $oldValues = $this->oldTemplate->toArray();

        /** @var CollectionTemplateColumn[] $oldColumns */
        $oldColumns = $this->oldTemplate->Columns;

        $oldValues['name'] = $this->getProperty('name');
        $oldValues['description'] = $this->getProperty('description');
        $oldValues['global_template'] = false;

        /** @var CollectionTemplate $newTemplate */
        $newTemplate = $this->modx->newObject($this->classKey);
        $newTemplate->fromArray($oldValues);

        $newColumns = array();

        foreach ($oldColumns as $column) {
            $columnArray = $column->toArray();
            unset($columnArray['template']);

            /** @var CollectionTemplateColumn $newColumn */
            $newColumn = $this->modx->newObject('CollectionTemplateColumn');
            $newColumn->fromArray($columnArray);

            $newColumns[] = $newColumn;
        }

        $newTemplate->addMany($newColumns, 'Columns');

        $newTemplate->save();

        return $this->success('', array ('id' => $newTemplate->get('id')));
    }

    public function beforeDuplicate() {
        $name = $this->getProperty('name', '');

        if (empty($name)) {
            $this->addFieldError('name', $this->modx->lexicon('collections.err.template_ns_name'));
        } else {
            if ($this->modx->getCount($this->classKey, array ('name' => $name)) > 0) {
                $this->addFieldError('name', $this->modx->lexicon('collections.err.template_ae_name'));
            }
        }

        return !$this->hasErrors();
    }

}
return 'CollectionsTemplateDuplicateProcessor';
