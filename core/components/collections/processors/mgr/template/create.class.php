<?php
/**
 * Create a Template
 *
 * @package collections
 * @subpackage processors.template
 */
class CollectionsTemplateCreateProcessor extends modObjectCreateProcessor {
    public $classKey = 'CollectionTemplate';
    public $languageTopics = array('collections:default');
    public $objectType = 'collections.template';
    /** @var CollectionTemplate $object */
    public $object;

    public function beforeSet() {
        $name = $this->getProperty('name');

        if (empty($name)) {
            $this->addFieldError('name',$this->modx->lexicon('collections.err.template_ns_name'));
        } else {
            if ($this->doesAlreadyExist(array('name' => $name))) {
                $this->addFieldError('name',$this->modx->lexicon('collections.err.template_ae_name'));
            }
        }

        $global = $this->getProperty('global_template');
        if ($global == 'true') {
            $this->setProperty('global_template', true);
        } else {
            $this->setProperty('global_template', false);

            $templatesCount = $this->modx->getCount('CollectionTemplate', array('global_template' => 1, 'id:!=' => $this->object->id));
            if ($templatesCount == 0) {
                $this->setProperty('global_template', true);
            }
        }

        $this->handleComboBoolean('bulk_actions');
        $this->handleComboBoolean('allow_dd');
        $this->handleComboBoolean('resource_type_selection');

        $childTemplate = $this->getProperty('child_template');
        if ($childTemplate == '') {
            $this->setProperty('child_template', null);
        }

        $tabLabel = $this->getProperty('tab_label');
        if (empty($tabLabel)) {
            $this->setProperty('tab_label', 'collections.children');
        }

        $buttonLabel = $this->getProperty('button_label');
        if (empty($buttonLabel)) {
            $this->setProperty('button_label', 'collections.children.create');
        }

        $templates = $this->getProperty('templates');
        $templates = $this->modx->collections->explodeAndClean($templates);

        $c = $this->modx->newQuery('CollectionResourceTemplate');
        $c->leftJoin('modTemplate', 'ResourceTemplate');
        $c->where(array(
            'resource_template:IN' => $templates,
        ));
        $c->select($this->modx->getSelectColumns('modTemplate', 'ResourceTemplate', '', array('templatename')));

        $c->prepare();
        $c->stmt->execute();
        $existingTemplates = $c->stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        $existingTemplatesCount = count($existingTemplates);
        if ($existingTemplatesCount > 0) {
            $type = ($existingTemplatesCount > 1) ? 'p' : 's';
            return $this->modx->lexicon('collections.err.template_resource_template_aiu_' . $type, array('templates' => implode(',', $existingTemplates)));
        }

        return parent::beforeSet();
    }

    public function afterSave() {
        $global = $this->getProperty('global_template');

        if ($global == true) {
            $this->modx->updateCollection('CollectionTemplate', array('global_template' => false), array('id:!=' => $this->object->id));
        }

        $templates = $this->getProperty('templates');
        $templates = $this->modx->collections->explodeAndClean($templates);

        $this->object->setTemplates($templates);

        $this->addIdColumn();

        return parent::afterSave();
    }

    public function addIdColumn() {
        $column = $this->modx->newObject('CollectionTemplateColumn');
        $column->set('name', 'id');
        $column->set('label', 'id');
        $column->set('hidden', true);
        $column->set('width', 40);
        $column->set('template', $this->object->id);
        $column->save();
    }

    public function handleComboBoolean($property) {
        $boolean = $this->getProperty($property);
        if ($boolean == 'true') {
            $this->setProperty($property, true);
        } else {
            $this->setProperty($property, false);
        }
    }

}
return 'CollectionsTemplateCreateProcessor';