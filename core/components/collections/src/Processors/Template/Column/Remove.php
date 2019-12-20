<?php
namespace Collections\Processors\Template\Column;

use Collections\Model\CollectionTemplateColumn;
use MODX\Revolution\Processors\Model\RemoveProcessor;

class Remove extends RemoveProcessor
{
    public $classKey = CollectionTemplateColumn::class;
    public $languageTopics = ['collections:default'];
    public $objectType = 'collections.template.column';

    public function beforeRemove()
    {

        if ($this->object->name == 'id') {
            return $this->modx->lexicon('collections.err.cant_remove_id_column');
        }

        return parent::beforeRemove();
    }
}
