<?php

class CollectionsExtraTemplateGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'modTemplate';
    public $languageTopics = array('template', 'category');
    public $defaultSortField = 'templatename';

    public function beforeIteration(array $list)
    {
        $addEmpty = $this->getProperty('addEmpty', false);

        if ($addEmpty) {
            $list[] = array(
                'id' => 0,
                'templatename' => $this->modx->lexicon('template_empty'),
            );
        }

        return $list;
    }

    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $query = $this->getProperty('query');
        $template = (int)$this->getProperty('template');

        $where = array();

        if ($template != 0) {
            $where[] = "NOT EXISTS (SELECT 1 FROM {$this->modx->getTableName('CollectionResourceTemplate')} r WHERE r.resource_template = modTemplate.id AND r.collection_template != " . $template . ")";
        }

        if (!empty($query)) {
            $valuesqry = $this->getProperty('valuesqry');

            if ($valuesqry == true) {
                $where['id:IN'] = $this->modx->collections->explodeAndClean($query, '|');
            } else {
                $where['templatename:LIKE'] = '%' . $query . '%';

            }
        }

        $c->where($where);

        return $c;
    }

}

return 'CollectionsExtraTemplateGetListProcessor';