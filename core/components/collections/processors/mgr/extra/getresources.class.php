<?php

/**
 * Get list of Children
 *
 * @package collections
 * @subpackage processors.resource
 */
class CollectionsExtrasResourceGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'modResource';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'desc';
    public $checkListPermission = true;
    public $languageTopics = array('resource', 'collections:default', 'collections:selections');
    /** @var CollectionSelection */
    public $selection = null;
    /** @var CollectionTemplate */
    public $collectionTemplate = null;

    public function initialize()
    {
        $this->setDefaultProperties(array(
            'start' => 0,
            'limit' => 20,
            'sort' => $this->defaultSortField . ':' . $this->defaultSortDirection,
            'combo' => false,
            'query' => '',
        ));

        $selection = intval($this->getProperty('selection', 0));
        if ($selection > 0) {
            $selection = $this->modx->getObject('modResource', $selection);
            if ($selection && ($selection->class_key == 'SelectionContainer')) {
                $this->selection = $selection;
                $this->collectionTemplate = $this->modx->collections->getCollectionsView($this->selection);
            }
        }

        return parent::initialize();
    }

    /**
     * Get the data of the query
     * @return array
     */
    public function getData()
    {
        $data = array();
        $limit = intval($this->getProperty('limit'));
        $start = intval($this->getProperty('start'));

        /* query for chunks */
        $c = $this->modx->newQuery($this->classKey);
        $c = $this->prepareQueryBeforeCount($c);
        $data['total'] = $this->modx->getCount($this->classKey, $c);
        $c = $this->prepareQueryAfterCount($c);

        $sorts = $this->getProperty('sort');

        $sorts = explode(',', $sorts);
        if (count($sorts) == 0) {
            $c->sortby($this->defaultSortField, $this->defaultSortDirection);
        } else {
            foreach ($sorts as $sort) {
                $sortParams = explode(':', $sort);
                if (count($sortParams) == 2) {
                    $c->sortby($sortParams[0], $sortParams[1]);
                } else {
                    $c->sortby($sortParams[0], $this->defaultSortDirection);
                }
            }
        }

        if ($limit > 0) {
            $c->limit($limit, $start);
        }

        $data['results'] = $this->modx->getCollection($this->classKey, $c);
        return $data;
    }

    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $query = $this->getProperty('query');
        if (!empty($query)) {
            if (stripos($query, 'http') === 0) {
                $uri = parse_url($query, PHP_URL_PATH);
                $uri = ltrim($uri, '/');
                $c->where(array(
                    'uri:=' => $uri
                ));
            } else {
                $c->where(array(
                    'pagetitle:LIKE' => '%' . $query . '%',
                    'OR:id:=' => $query
                ));
            }
        }

        if ($this->collectionTemplate !== null) {
            $where = $this->modx->fromJSON($this->collectionTemplate->selection_link_condition);
            if (is_array($where)) {
                $c->where($where);
            }
        }

        return $c;
    }

    /**
     * Prepare the row for iteration
     * @param xPDOObject $object
     * @return array
     */
    public function prepareRow(xPDOObject $object)
    {
        $ta = $object->toArray();

        $ta['pagetitle'] .= ' (' . $ta['id'] . ')';
        return $ta;
    }

}

return 'CollectionsExtrasResourceGetListProcessor';
