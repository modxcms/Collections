<?php

/**
 * Get list Templates
 *
 * @package collections
 * @subpackage processors
 */
class CollectionsTemplateGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'CollectionTemplate';
    public $languageTopics = array('collections:default', 'template');
    public $defaultSortField = 'name';
    public $defaultSortDirection = 'ASC';
    public $objectType = 'collections.template';

    public function beforeIteration(array $list)
    {
        $addEmpty = $this->getProperty('addEmpty', false);

        if ($addEmpty) {
            $list[] = array(
                'id' => 0,
                'name' => $this->modx->lexicon('collections.template.empty'),
            );
        }
        return $list;
    }

    public function prepareRow(xPDOObject $object)
    {
        $template = $object->toArray();

        $c = $this->modx->newQuery('CollectionResourceTemplate');
        $c->leftJoin('modTemplate', 'ResourceTemplate');
        $c->where(array(
            'collection_template' => $template['id']
        ));
        $c->select(array(
            'test' => 'IF(resource_template = 0, \'' . $this->modx->lexicon('template_empty') . '\', ResourceTemplate.templatename)'
        ));
        $c->prepare();
        $c->stmt->execute();

        $template['default_for_templates'] = $c->stmt->fetchAll(PDO::FETCH_COLUMN, 0);

        return $template;
    }
}

return 'CollectionsTemplateGetListProcessor';