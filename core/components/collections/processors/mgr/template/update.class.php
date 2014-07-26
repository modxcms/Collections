<?php
/**
 * Update a Template
 *
 * @package collections
 * @subpackage processors.template
 */
class CollectionsTemplateUpdateProcessor extends modObjectUpdateProcessor {
    public $classKey = 'CollectionTemplate';
    public $languageTopics = array('collections:default');
    public $objectType = 'collections.template';
    /** @var CollectionTemplate $object */
    public $object;

    public function beforeSet() {
        $name = $this->getProperty('name');

        if (empty($name)) {
            $this->addFieldError('name',$this->modx->lexicon('collections.err.template_ns_name'));
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

        $templates = $this->getProperty('templates');
        $templates = $this->modx->collections->explodeAndClean($templates);

        $c = $this->modx->newQuery('CollectionResourceTemplate');
        $c->leftJoin('modTemplate', 'ResourceTemplate');
        $c->where(array(
            'resource_template:IN' => $templates,
            'collection_template:!=' => $this->object->id
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

        return parent::afterSave();
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
return 'CollectionsTemplateUpdateProcessor';