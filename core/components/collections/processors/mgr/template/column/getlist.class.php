<?php

/**
 * Get list Template columns
 *
 * @package collections
 * @subpackage processors
 */
class CollectionsTemplateColumnGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'CollectionTemplateColumn';
    public $languageTopics = array('collections:default');
    public $defaultSortField = 'position';
    public $defaultSortDirection = 'ASC';
    public $objectType = 'collections.template.column';

    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $template = $this->getProperty('template');

        if (!empty($template)) {
            $c->where(array(
                'template' => $template
            ));
        }

        return $c;
    }
}

return 'CollectionsTemplateColumnGetListProcessor';