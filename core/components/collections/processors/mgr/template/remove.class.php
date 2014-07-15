<?php
/**
 * Remove a Template.
 *
 * @package collections
 * @subpackage processors
 */
class CollectionTemplateRemoveProcessor extends modObjectRemoveProcessor {
    public $classKey = 'CollectionTemplate';
    public $languageTopics = array('collections:default');
    public $objectType = 'collections.template';

    public function beforeRemove() {

        $templatesCount = $this->modx->getCount($this->classKey);
        if ($templatesCount == 1) {
            return $this->modx->lexicon('collections.err.template_remove_last');
        }

        if ($this->object->global_template == 1) {
            return $this->modx->lexicon('collections.err.template_remove_global');
        }

        return parent::beforeRemove();
    }
}
return 'CollectionTemplateRemoveProcessor';