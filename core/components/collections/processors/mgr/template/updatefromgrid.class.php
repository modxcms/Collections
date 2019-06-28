<?php
/**
 * Update From Grid a Template
 *
 * @package collections
 * @subpackage processors.template
 */

class CollectionsTemplateUpdateFromGridProcessor extends modObjectUpdateProcessor
{
    public $classKey = 'CollectionTemplate';
    public $languageTopics = array('collections:default');
    public $objectType = 'collections.template';
    /** @var CollectionTemplate $object */
    public $object;

    public function initialize()
    {
        $data = $this->getProperty('data');
        if (empty($data)) return $this->modx->lexicon('invalid_data');
        $data = $this->modx->fromJSON($data);
        if (empty($data)) return $this->modx->lexicon('invalid_data');

        if (isset($data['id'])) {
            $this->setProperty('id', $data['id']);
        }

        if (isset($data['name'])) {
            $this->setProperty('name', $data['name']);
        }

        if (isset($data['description'])) {
            $this->setProperty('description', $data['description']);
        }

        if (isset($data['global_template'])) {
            $this->setProperty('global_template', $data['global_template']);
        }

        $this->unsetProperty('data');

        return parent::initialize();
    }

    public function beforeSet()
    {
        $name = $this->getProperty('name');

        if (empty($name)) {
            $this->addFieldError('name', $this->modx->lexicon('collections.err.template_ns_name'));
        } else {
            if ($this->modx->getCount($this->classKey, array('name' => $name, 'id:!=' => $this->object->id)) > 0) {
                $this->addFieldError('name', $this->modx->lexicon('collections.err.template_ae_name'));
            }
        }

        $global = $this->getProperty('global_template');

        if ($global == false) {
            $templatesCount = $this->modx->getCount('CollectionTemplate', array('global_template' => 1, 'id:!=' => $this->object->id));
            if ($templatesCount == 0) {
                $this->setProperty('global_template', true);
            }
        }

        return parent::beforeSet();
    }

    public function afterSave()
    {
        $global = $this->getProperty('global_template');

        if ($global == true) {
            $this->modx->updateCollection('CollectionTemplate', array('global_template' => false), array('id:!=' => $this->object->id));
        }

        return parent::afterSave();
    }
}

return 'CollectionsTemplateUpdateFromGridProcessor';
