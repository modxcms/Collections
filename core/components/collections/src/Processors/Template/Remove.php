<?php
namespace Collections\Processors\Template;

use Collections\Model\CollectionTemplate;
use MODX\Revolution\Processors\Model\RemoveProcessor;

class Remove extends RemoveProcessor
{
    public $classKey = CollectionTemplate::class;
    public $languageTopics = array('collections:default');
    public $objectType = 'collections.template';

    public function beforeRemove()
    {

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
