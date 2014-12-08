<?php
/**
 * Get list of Children
 *
 * @package collections
 * @subpackage processors.resource
 */
class CollectionsResourceGetListProcessor extends modObjectGetListProcessor {
    public $classKey = 'modResource';
    public $defaultSortField = 'createdon';
    public $defaultSortDirection = 'DESC';
    public $checkListPermission = true;
    public $languageTopics = array('resource','collections:default');

    public $tvColumns = array();
    public $taggerColumns = array();
    public $useQuip = false;
    public $useTagger = false;

    public $columnRenderer = array();

    public $actions = array();
    public $buttons = array();

    public function initialize() {
        $parent = $this->getProperty('parent',null);
        if (empty($parent)) {
            return false;
        }

        $this->setActions();

        $parentObject = $this->modx->getObject('modResource', $parent);
        $template = $this->modx->collections->getCollectionsView($parentObject);

        $buttons = $this->modx->collections->explodeAndClean($template->buttons, ',', 1);
        foreach ($buttons as $button) {
            $button = $this->modx->collections->explodeAndClean($button, ':');

            if (!isset($this->actions[$button[0]])) continue;

            if (isset($button[1])) {
                $this->actions[$button[0]]['className'] .= ' ' . $button[1];
            }

            $this->buttons[] = $button[0];
        }


        $templateColumnsQuery = $this->modx->newQuery('CollectionTemplateColumn');
        $templateColumnsQuery->where(array(
            'template' => $template->id,
        ));
        $templateColumnsQuery->where(array(
            'name:LIKE' => 'tv_%',
            'OR:name:LIKE' => 'tagger_%',
            'OR:name:IN' => array('quip'),
            'OR:php_renderer:!=' => '',
        ));
        $templateColumnsQuery->select($this->modx->getSelectColumns('CollectionTemplateColumn', '', '', array('name', 'php_renderer')));
        $templateColumnsQuery->prepare();
        $templateColumnsQuery->stmt->execute();

        while ($column = $templateColumnsQuery->stmt->fetch(PDO::FETCH_ASSOC)) {
            if (strpos($column['name'], 'tv_') !== false) {
                $tvName = preg_replace('/tv_/', '', $column['name'], 1);

                $tv = $this->modx->getObject('modTemplateVar', array('name' => $tvName));

                if ($tv) {
                    $this->tvColumns[] = array('id' => $tv->id, 'name' => $tvName, 'column' => $column['name']);
                }
            }

            if (strpos($column['name'], 'tagger_') !== false) {
                $this->taggerColumns[] = $column['name'];
            }

            if (strtolower($column['name']) == 'quip') {
                $this->useQuip = true;
            }

            if ($column['php_renderer'] != '') {
                $snippet = $this->modx->getObject('modSnippet', array('name' => $column['php_renderer']));
                if ($snippet) {
                    $this->columnRenderer[$column['name']] = $column['php_renderer'];
                }
            }
        }

        $quipInstalled = $this->modx->collections->getOption('quipInstalled', null,  false);
        if (!$quipInstalled) {
            $this->useQuip = false;
        }

        $this->useTagger = $this->modx->collections->getOption('taggerInstalled', null,  false);

        return parent::initialize();
    }

    public function setActions() {
        $this->actions['view'] = array(
            'className' => 'view',
            'text' => $this->modx->lexicon('view'),
            'key' => 'view',
        );
        $this->actions['edit'] = array(
            'className' => 'edit',
            'text' => $this->modx->lexicon('edit'),
            'key' => 'edit',
        );
        $this->actions['duplicate'] = array(
            'className' => 'duplicate',
            'text' => $this->modx->lexicon('duplicate'),
            'key' => 'duplicate',
        );
        $this->actions['unpublish'] = array(
            'className' => 'unpublish',
            'text' => $this->modx->lexicon('unpublish'),
            'key' => 'unpublish',
        );
        $this->actions['publish'] = array(
            'className' => 'publish',
            'text' => $this->modx->lexicon('publish'),
            'key' => 'publish',
        );
        $this->actions['undelete'] = array(
            'className' => 'undelete',
            'text' => $this->modx->lexicon('undelete'),
            'key' => 'undelete',
        );
        $this->actions['remove'] = array(
            'className' => 'remove',
            'text' => $this->modx->lexicon('collections.children.remove_action'),
            'key' => 'remove',
        );
        $this->actions['delete'] = array(
            'className' => 'delete',
            'text' => $this->modx->lexicon('delete'),
            'key' => 'delete',
        );
    }

    public function prepareQueryBeforeCount(xPDOQuery $c) {
        $parent = $this->getProperty('parent',null);

        $c->where(array(
            'parent' => $parent,
        ));

        $query = $this->getProperty('query',null);
        if (!empty($query)) {
            $c->leftJoin('modUserProfile', 'CreatedByProfile', array('CreatedByProfile.internalKey = modResource.createdby'));
            $c->leftJoin('modUser', 'CreatedBy');

            $queryWhere = array(
                'pagetitle:LIKE' => '%'.$query.'%',
                'OR:description:LIKE' => '%'.$query.'%',
                'OR:alias:LIKE' => '%'.$query.'%',
                'OR:introtext:LIKE' => '%'.$query.'%',
                'OR:CreatedByProfile.fullname:LIKE' => '%'.$query.'%',
                'OR:CreatedBy.username:LIKE' => '%'.$query.'%',
            );

            if ($this->useTagger) {
                $c->leftJoin('TaggerTagResource', 'TagResource', array('TagResource.resource = modResource.id'));
                $c->leftJoin('TaggerTag', 'Tag', array('Tag.id = TagResource.tag'));

                array_push($queryWhere, array(
                    'OR:Tag.tag:LIKE' => '%'.$query.'%',
                ));
            }

            $c->where($queryWhere);
        }
        $filter = $this->getProperty('filter','');
        switch ($filter) {
            case 'published':
                $c->where(array(
                    'published' => 1,
                    'deleted' => 0,
                ));
                break;
            case 'unpublished':
                $c->where(array(
                    'published' => 0,
                    'deleted' => 0,
                ));
                break;
            case 'deleted':
                $c->where(array(
                    'deleted' => 1,
                ));
                break;
            default:
                $c->where(array(
                    'deleted' => 0,
                ));
                break;
        }

        $c->where(array(
            'class_key:!=' => 'CollectionContainer',
            "NOT EXISTS (SELECT 1 FROM {$this->modx->getTableName('modResource')} r WHERE r.parent = modResource.id)"
        ));

        foreach ($this->tvColumns as $column) {
            $c->leftJoin('modTemplateVarResource', '`TemplateVarResources_' . $column['column'] . '`', '`TemplateVarResources_' . $column['column'] . '`.`contentid` = modResource.id AND `TemplateVarResources_' . $column['column'] . '`.`tmplvarid` = ' . $column['id']);
        }

        return $c;
    }

    public function prepareQueryAfterCount(xPDOQuery $c) {

        $c->select($this->modx->getSelectColumns('modResource', 'modResource'));

        foreach ($this->tvColumns as $column) {
            $c->select(array(
                '`' . $column['column'] . '`' => '`TemplateVarResources_' . $column['column'] . '`.`value`'
            ));
        }

        if ($this->useTagger) {
            foreach ($this->taggerColumns as $column) {
                $c->select(array(
                    '`' . $column . '`' => '(SELECT group_concat(t.tag SEPARATOR \', \') FROM `modx_tagger_tag_resources` tr LEFT JOIN `modx_tagger_tags` t ON t.id = tr.tag LEFT JOIN `modx_tagger_groups` tg ON tg.id = t.group WHERE tr.resource = modResource.id AND tg.alias = \'' . preg_replace('/tagger_/', '', $column, 1) . '\' group by t.group)'
                ));
            }
        }

        if ($this->useQuip) {
            $commentsQuery = $this->modx->newQuery('quipComment');
            $commentsQuery->innerJoin('quipThread','Thread');
            $commentsQuery->where(array(
                'Thread.resource = modResource.id',
            ));
            $commentsQuery->select(array(
                'COUNT('.$this->modx->getSelectColumns('quipComment','quipComment','',array('id')).')',
            ));
            $commentsQuery->prepare();
            $c->select(array(
                '('.$commentsQuery->toSQL().') AS '.$this->modx->escape('quip'),
            ));
        }

        return $c;
    }

    /**
     * @param xPDOObject $object
     * @return array
     */
    public function prepareRowWithRenderer(xPDOObject $object) {
        $resourceArray = parent::prepareRow($object);

        foreach ($resourceArray as $key => $column) {
            if (!isset($this->columnRenderer[$key])) continue;

            $resourceArray[$key] = $this->modx->runSnippet($this->columnRenderer[$key], array('value' => $column, 'row' => $resourceArray));
        }

        $resourceArray = $this->prepareSupportFields($resourceArray);
        $resourceArray = $this->prepareActions($resourceArray);
        $resourceArray = $this->prepareMenuActions($resourceArray);

        return $resourceArray;
    }

    public function prepareRow(xPDOObject $object) {
        $resourceArray = parent::prepareRow($object);

        $resourceArray = $this->prepareSupportFields($resourceArray);
        $resourceArray = $this->prepareActions($resourceArray);
        $resourceArray = $this->prepareMenuActions($resourceArray);

        return $resourceArray;
    }

    public function prepareSupportFields($resourceArray) {
        $version = $this->modx->getVersionData();

        if ($version['major_version'] < 3) {
            $resourceArray['action_edit'] = '?a=30&id='.$resourceArray['id'];
        } else {
            $resourceArray['action_edit'] = '?a=resource/update&action=post/update&id='.$resourceArray['id'];
        }

        $this->modx->getContext($resourceArray['context_key']);
        $resourceArray['preview_url'] = $this->modx->makeUrl($resourceArray['id'],$resourceArray['context_key']);

        return $resourceArray;
    }

    public function prepareActions($resourceArray) {
        $resourceArray['actions'] = array();

        foreach ($this->buttons as $button) {
            if (isset($this->actions[$button])) {
                switch ($button) {
                    case 'publish':
                        if (empty($resourceArray['published'])) {
                            $resourceArray['actions'][] = $this->actions[$button];
                        }
                        break;
                    case 'unpublish':
                        if (!empty($resourceArray['published'])) {
                            $resourceArray['actions'][] = $this->actions[$button];
                        }
                        break;
                    case 'delete':
                        if (empty($resourceArray['deleted'])) {
                            $resourceArray['actions'][] = $this->actions[$button];
                        }
                        break;
                    case 'undelete':
                        if (!empty($resourceArray['deleted'])) {
                            $resourceArray['actions'][] = $this->actions[$button];
                        }
                        break;
                    case 'remove':
                        if (!empty($resourceArray['deleted'])) {
                            $resourceArray['actions'][] = $this->actions[$button];
                        }
                        break;
                    default:
                        $resourceArray['actions'][] = $this->actions[$button];
                }

            }
        }

        return $resourceArray;
    }

    public function prepareMenuActions($resourceArray) {
        $resourceArray['menu_actions'] = array();

        $resourceArray['menu_actions']['view'] = $this->actions['view'];
        $resourceArray['menu_actions']['edit'] = $this->actions['edit'];
        $resourceArray['menu_actions']['duplicate'] = $this->actions['duplicate'];

        if (!empty($resourceArray['published'])) {
            $resourceArray['menu_actions']['unpublish'] = $this->actions['unpublish'];
        } else {
            $resourceArray['menu_actions']['publish'] = $this->actions['publish'];
        }

        if (!empty($resourceArray['deleted'])) {
            $resourceArray['menu_actions']['undelete'] = $this->actions['undelete'];
            $resourceArray['menu_actions']['remove'] = $this->actions['remove'];
        } else {
            $resourceArray['menu_actions']['delete'] = $this->actions['delete'];
        }

        return $resourceArray;
    }

    /**
     * Get the data of the query
     * @return array
     */
    public function getData() {
        $data = array();
        $limit = intval($this->getProperty('limit'));
        $start = intval($this->getProperty('start'));

        /* query for chunks */
        $c = $this->modx->newQuery($this->classKey);
        $c = $this->prepareQueryBeforeCount($c);
        $data['total'] = $this->modx->getCount($this->classKey,$c);
        $c = $this->prepareQueryAfterCount($c);

        $c->sortby('`' . $this->getProperty('sort') . '`',$this->getProperty('dir'));
        if ($limit > 0) {
            $c->limit($limit,$start);
        }

        $data['results'] = $this->modx->getCollection($this->classKey,$c);
        return $data;
    }

    public function iterate(array $data) {
        $list = array();
        $list = $this->beforeIteration($list);
        $this->currentIndex = 0;
        /** @var xPDOObject|modAccessibleObject $object */
        foreach ($data['results'] as $object) {
            if ($this->checkListPermission && $object instanceof modAccessibleObject && !$object->checkPolicy('list')) continue;

            $objectArray = $this->prepareRow($object);

            if (!empty($objectArray) && is_array($objectArray)) {
                $list[] = $objectArray;
                $this->currentIndex++;
            }
        }
        $list = $this->afterIteration($list);
        return $list;
    }

    public function iterateWithRenderer(array $data) {
        $list = array();
        $list = $this->beforeIteration($list);
        $this->currentIndex = 0;
        /** @var xPDOObject|modAccessibleObject $object */
        foreach ($data['results'] as $object) {
            if ($this->checkListPermission && $object instanceof modAccessibleObject && !$object->checkPolicy('list')) continue;

            $objectArray = $this->prepareRowWithRenderer($object);

            if (!empty($objectArray) && is_array($objectArray)) {
                $list[] = $objectArray;
                $this->currentIndex++;
            }
        }
        $list = $this->afterIteration($list);
        return $list;
    }

    public function process() {
        $beforeQuery = $this->beforeQuery();
        if ($beforeQuery !== true) {
            return $this->failure($beforeQuery);
        }
        $data = $this->getData();

        if (count($this->columnRenderer) > 0) {
            $list = $this->iterateWithRenderer($data);
        } else {
            $list = $this->iterate($data);
        }

        return $this->outputArray($list,$data['total']);
    }
}
return 'CollectionsResourceGetListProcessor';