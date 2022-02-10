<?php
namespace Collections\Processors\Template;
use Collections\Model\CollectionResourceTemplate;
use Collections\Model\CollectionTemplate;
use MODX\Revolution\modTemplate;
use MODX\Revolution\Processors\Model\GetListProcessor;
use xPDO\Om\xPDOObject;

class GetList extends GetListProcessor
{
    public $classKey = CollectionTemplate::class;
    public $languageTopics = ['collections:default', 'template'];
    public $defaultSortField = 'name';
    public $defaultSortDirection = 'ASC';
    public $objectType = 'collections.template';

    public function beforeIteration(array $list)
    {
        $addEmpty = $this->getProperty('addEmpty', false);

        if ($addEmpty) {
            $list[] = [
                'id' => 0,
                'name' => $this->modx->lexicon('collections.template.empty'),
            ];
        }
        return $list;
    }

    public function prepareRow(xPDOObject $object)
    {
        $template = $object->toArray();

        $c = $this->modx->newQuery(CollectionResourceTemplate::class);
        $c->leftJoin(modTemplate::class, 'ResourceTemplate');
        $c->where([
            'collection_template' => $template['id']
        ]);
        $c->select([
            'test' => 'IF(resource_template = 0, \'' . $this->modx->lexicon('template_empty') . '\', ResourceTemplate.templatename)'
        ]);
        $c->prepare();
        $c->stmt->execute();

        $template['default_for_templates'] = $c->stmt->fetchAll(\PDO::FETCH_COLUMN, 0);

        return $template;
    }
}
