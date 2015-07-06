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

        $label = $this->getProperty('label');
        if (empty($label)) {
            $autoLabel = false;

            if (strpos($name, 'tv_') !== false) {
                $tvName = preg_replace('/tv_/', '', $name, 1);
                /** @var modTemplateVar $tv */
                $tv = $this->modx->getObject('modTemplateVar', array('name' => $tvName));
                if ($tv) {
                    $this->setProperty('label', $tv->caption);
                    $autoLabel = true;
                }
            }

            $useTagger = $this->modx->collections->getOption('taggerInstalled', null,  false);
            if ($useTagger && (strpos($name, 'tagger_') !== false)) {
                $groupName = preg_replace('/tagger_/', '', $name, 1);
                /** @var TaggerGroup $taggerGroup */
                $taggerGroup = $this->modx->getObject('TaggerGroup', array('alias' => $groupName));
                if ($taggerGroup) {
                    $this->setProperty('label', $taggerGroup->name);
                    $autoLabel = true;
                }
            }

            if (!$autoLabel) {
                $this->addFieldError('label',$this->modx->lexicon('collections.err.template_ns_label'));
            }
        }

        if ($this->object->name == 'id' && $name != 'id') {
            $this->addFieldError('name',$this->modx->lexicon('collections.err.column_name_cant_change'));
        }

        $this->handleNull('sort_type');

        return parent::beforeSet();
    }

    public function afterSave() {

        return parent::afterSave();
    }

    public function handleNull($property) {
        $value = $this->getProperty($property);

        if ($value == '') {
            $this->setProperty($property, null);

            return null;
        }

        $this->setProperty($property, $value);

        return $value;
    }

}
return 'CollectionsTemplateColumnUpdateProcessor';