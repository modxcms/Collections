<?php
namespace Collections\Processors\Template\Column;

use Collections\Model\CollectionTemplateColumn;
use MODX\Revolution\Processors\Model\GetListProcessor;
use xPDO\Om\xPDOQuery;

class GetList extends GetListProcessor
{
    public $classKey = CollectionTemplateColumn::class;
    public $languageTopics = ['collections:default'];
    public $defaultSortField = 'position';
    public $defaultSortDirection = 'ASC';
    public $objectType = 'collections.template.column';

    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $template = $this->getProperty('template');

        if (!empty($template)) {
            $c->where([
                'template' => $template
            ]);
        }

        return $c;
    }
}
