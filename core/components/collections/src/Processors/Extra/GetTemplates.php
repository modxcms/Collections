<?php
namespace Collections\Processors\Extra;

use Collections\Model\CollectionResourceTemplate;
use Collections\Utils;
use MODX\Revolution\modTemplate;
use MODX\Revolution\Processors\Model\GetListProcessor;
use xPDO\Om\xPDOQuery;

class GetTemplates extends GetListProcessor
{
    public $classKey = modTemplate::class;
    public $languageTopics = ['template', 'category'];
    public $defaultSortField = 'templatename';

    public function beforeIteration(array $list)
    {
        $addEmpty = $this->getProperty('addEmpty', false);

        if ($addEmpty) {
            $list[] = [
                'id' => 0,
                'templatename' => $this->modx->lexicon('template_empty'),
            ];
        }

        return $list;
    }

    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $query = $this->getProperty('query');
        $template = (int)$this->getProperty('template');

        $where = [];

        if ($template != 0) {
            $where[] = "NOT EXISTS (SELECT 1 FROM {$this->modx->getTableName(CollectionResourceTemplate::class)} r WHERE r.resource_template = modTemplate.id AND r.collection_template != " . $template . ")";
        }

        if (!empty($query)) {
            $valuesqry = $this->getProperty('valuesqry');

            if ($valuesqry == true) {
                $where['id:IN'] = Utils::explodeAndClean($query, '|');
            } else {
                $where['templatename:LIKE'] = '%' . $query . '%';

            }
        }

        $c->where($where);

        return $c;
    }

}
