<?php
namespace Collections\Processors\Selection;
use Collections\Model\CollectionSelection;
use Collections\Model\CollectionTemplate;
use Collections\Model\CollectionTemplateColumn;
use Collections\Utils;
use MODX\Revolution\modAccessibleObject;
use MODX\Revolution\modResource;
use MODX\Revolution\modSnippet;
use MODX\Revolution\modTemplateVar;
use MODX\Revolution\modTemplateVarResource;
use MODX\Revolution\modUser;
use MODX\Revolution\modUserProfile;
use MODX\Revolution\Processors\Model\GetListProcessor;
use xPDO\Om\xPDOObject;
use xPDO\Om\xPDOQuery;

/**
 * Get list of Selections
 *
 * @package collections
 * @subpackage processors.selection
 */
class GetList extends GetListProcessor
{
    public $classKey = modResource::class;
    public $defaultSortField = 'menuindex';
    public $defaultSortDirection = 'ASC';
    public $checkListPermission = true;
    public $languageTopics = ['resource', 'collections:default'];

    public $tvColumns = [];
    public $taggerColumns = [];

    public $columnRenderer = [];

    public $actions = [];
    public $buttons = [];
    public $sortType = null;
    public $sortBefore = '';
    public $sortAfter = '';
    public $searchQueryExcludeTvs = false;
    public $searchQueryExcludeTagger = false;
    public $searchQueryTitleOnly = false;

    /** @var \Collections\Collections */
    protected $collections;

    public $permissions = [
        'publish_document' => false,
        'unpublish_document' => false,
        'delete_document' => false,
        'undelete_document' => false,
        'view_document' => false,
        'edit_document' => false,
        'purge_deleted' => false,
    ];

    public function initialize()
    {
        $parent = $this->getProperty('parent', null);
        if (empty($parent)) {
            return false;
        }

        $this->collections = $this->modx->services->get('collections');

        $this->permissions = [
            'publish_document' => $this->modx->hasPermission('publish_document'),
            'unpublish_document' => $this->modx->hasPermission('unpublish_document'),
            'delete_document' => $this->modx->hasPermission('delete_document'),
            'undelete_document' => $this->modx->hasPermission('undelete_document'),
            'view_document' => $this->modx->hasPermission('view_document'),
            'edit_document' => $this->modx->hasPermission('edit_document'),
            'purge_deleted' => $this->modx->hasPermission('purge_deleted'),
        ];

        $this->setActions();

        /** @var modResource $parentObject */
        $parentObject = $this->modx->getObject(modResource::class, $parent);
        /** @var CollectionTemplate $template */
        $template = $this->collections->getCollectionsView($parentObject);

        $sort = $this->getProperty('sort');
        $sort = explode(':', $sort);

        if (isset($sort[1])) {
            $this->sortType = $sort[1];
            $this->setProperty('sort', $sort[0]);
        } else {
            $this->setProperty('sort', $sort[0]);

            /** @var CollectionTemplateColumn[] $columns */
            $columns = $template->getMany('Columns', ['name' => $sort[0]]);
            if (count($columns) == 1) {
                foreach ($columns as $column) {
                    $this->sortType = $column->sort_type;
                }
            }
        }

        $this->sortBefore = $template->permanent_sort_before;
        $this->sortAfter = $template->permanent_sort_after;

        $this->searchQueryExcludeTvs = $template->search_query_exclude_tvs;
        $this->searchQueryExcludeTagger = $template->search_query_exclude_tagger;
        $this->searchQueryTitleOnly = $template->search_query_title_only;

        $buttons = Utils::explodeAndClean($template->buttons, ',', 1);
        foreach ($buttons as $button) {
            $button = Utils::explodeAndClean($button, ':');

            if (!isset($this->actions[$button[0]])) continue;

            if (isset($button[1])) {
                $this->actions[$button[0]]['className'] .= ' ' . $button[1];
            }

            $this->buttons[] = $button[0];
        }

        $templateColumnsQuery = $this->modx->newQuery(CollectionTemplateColumn::class);
        $templateColumnsQuery->where([
            'template' => $template->id,
        ]);
        $templateColumnsQuery->where([
            'name:LIKE' => 'tv_%',
            'OR:name:LIKE' => 'tagger_%',
            'OR:php_renderer:!=' => '',
        ]);
        $templateColumnsQuery->select($this->modx->getSelectColumns(CollectionTemplateColumn::class, '', '', ['name', 'php_renderer']));
        $templateColumnsQuery->prepare();
        $templateColumnsQuery->stmt->execute();

        while ($column = $templateColumnsQuery->stmt->fetch(\PDO::FETCH_ASSOC)) {
            if (strpos($column['name'], 'tv_') !== false) {
                $tvName = preg_replace('/tv_/', '', $column['name'], 1);

                $tv = $this->modx->getObject(modTemplateVar::class, ['name' => $tvName]);

                if ($tv) {
                    $this->tvColumns[] = ['id' => $tv->id, 'name' => $tvName, 'column' => $column['name'], 'default' => $tv->default_text];
                }
            }

            if (strpos($column['name'], 'tagger_') !== false) {
                $this->taggerColumns[] = $column['name'];
            }

            if ($column['php_renderer'] != '') {
                $snippet = $this->modx->getObject(modSnippet::class, ['name' => $column['php_renderer']]);
                if ($snippet) {
                    $this->columnRenderer[$column['name']] = $column['php_renderer'];
                }
            }
        }

        return parent::initialize();
    }

    public function setActions()
    {
        $this->actions['view'] = [
            'className' => 'view',
            'text' => $this->modx->lexicon('view'),
            'key' => 'view',
        ];
        $this->actions['edit'] = [
            'className' => 'edit',
            'text' => $this->modx->lexicon('edit'),
            'key' => 'edit',
        ];
        $this->actions['quickupdate'] = [
            'className' => 'quickupdate',
            'text' => $this->modx->lexicon('quick_update_resource'),
            'key' => 'quickupdate',
        ];
        $this->actions['unpublish'] = [
            'className' => 'unpublish',
            'text' => $this->modx->lexicon('unpublish'),
            'key' => 'unpublish',
        ];
        $this->actions['publish'] = [
            'className' => 'publish',
            'text' => $this->modx->lexicon('publish'),
            'key' => 'publish',
        ];
        $this->actions['undelete'] = [
            'className' => 'undelete',
            'text' => $this->modx->lexicon('undelete'),
            'key' => 'undelete',
        ];
        $this->actions['remove'] = [
            'className' => 'remove',
            'text' => $this->modx->lexicon('collections.children.remove_action'),
            'key' => 'remove',
        ];
        $this->actions['delete'] = [
            'className' => 'delete',
            'text' => $this->modx->lexicon('delete'),
            'key' => 'delete',
        ];
        $this->actions['unlink'] = [
            'className' => 'unlink',
            'text' => $this->modx->lexicon('selections.unlink_action'),
            'key' => 'unlink',
        ];
    }

    public function process()
    {
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

        return $this->outputArray($list, $data['total']);
    }

    /**
     * Get the data of the query
     * @return array
     */
    public function getData()
    {
        $data = [];
        $limit = intval($this->getProperty('limit'));
        $start = intval($this->getProperty('start'));

        /* query for chunks */
        $c = $this->modx->newQuery($this->classKey);
        $c = $this->prepareQueryBeforeCount($c);
        $data['total'] = $this->modx->getCount($this->classKey, $c);
        $c = $this->prepareQueryAfterCount($c);

        $gridSort = $this->getProperty('sort');

        $selectionGridSort = (strtolower($gridSort) == 'menuindex') ? 'CollectionSelection`.`menuindex' : $gridSort;

        $c = $this->permanentSort($c, $gridSort, $this->sortBefore);

        if (empty($this->sortType)) {
            $c->sortby('`' . $selectionGridSort . '`', $this->getProperty('dir'));
        } else {
            $c->sortby('CAST(`' . $selectionGridSort . '` as ' . $this->sortType . ')', $this->getProperty('dir'));
        }

        $c = $this->permanentSort($c, $gridSort, $this->sortAfter);

        if ($limit > 0) {
            $c->limit($limit, $start);
        }

        $data['results'] = $this->modx->getCollection($this->classKey, $c);
        return $data;
    }

    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $c->leftJoin(CollectionSelection::class, 'CollectionSelection', 'modResource.id = CollectionSelection.resource');

        $parent = $this->getProperty('parent', null);

        $c->where([
            'CollectionSelection.collection' => $parent,
        ]);

        $query = $this->getProperty('query', null);
        if (!empty($query)) {
            $c->leftJoin(modUserProfile::class, 'CreatedByProfile', ['CreatedByProfile.internalKey = modResource.createdby']);
            $c->leftJoin(modUser::class, 'CreatedBy');

            if ($this->searchQueryTitleOnly) {
                $queryWhere = [
                    'pagetitle:LIKE' => '%' . $query . '%',
                ];
            } else {
                $queryWhere = [
                    'pagetitle:LIKE' => '%' . $query . '%',
                    'OR:description:LIKE' => '%' . $query . '%',
                    'OR:alias:LIKE' => '%' . $query . '%',
                    'OR:introtext:LIKE' => '%' . $query . '%',
                    'OR:CreatedByProfile.fullname:LIKE' => '%' . $query . '%',
                    'OR:CreatedBy.username:LIKE' => '%' . $query . '%',
                ];
            }

            if ($this->searchQueryExcludeTvs == false) {
                // tv columns search rules
                foreach ($this->tvColumns as $column) {
                    array_push($queryWhere, [
                        'OR:TemplateVarResources_' . $column['column'] . '.value:LIKE' => '%' . $query . '%',
                    ]);
                }
            }

            if ($this->searchQueryExcludeTagger == false) {
                $taggerInstalled = $this->collections->getOption('taggerInstalled', null, false);
                if ($taggerInstalled) {
                    $c->leftJoin('Tagger\\Model\\TaggerTagResource', 'TagResource', ['TagResource.resource = modResource.id']);
                    $c->leftJoin('Tagger\\Model\\TaggerTag', 'Tag', ['Tag.id = TagResource.tag']);

                    array_push($queryWhere, [
                        'OR:Tag.tag:LIKE' => '%' . $query . '%',
                    ]);
                }
            }

            $c->where($queryWhere);
        }
        $filter = $this->getProperty('filter', '');
        switch ($filter) {
            case 'published':
                $c->where([
                    'published' => 1,
                    'deleted' => 0,
                ]);
                break;
            case 'unpublished':
                $c->where([
                    'published' => 0,
                    'deleted' => 0,
                ]);
                break;
            case 'deleted':
                $c->where([
                    'deleted' => 1,
                ]);
                break;
            default:
                $c->where([
                    'deleted' => 0,
                ]);
                break;
        }

        foreach ($this->tvColumns as $column) {
            $c->leftJoin(modTemplateVarResource::class, '`TemplateVarResources_' . $column['column'] . '`', '`TemplateVarResources_' . $column['column'] . '`.`contentid` = modResource.id AND `TemplateVarResources_' . $column['column'] . '`.`tmplvarid` = ' . $column['id']);
        }

        return $c;
    }

    public function prepareQueryAfterCount(xPDOQuery $c)
    {

        $c->select($this->modx->getSelectColumns(modResource::class, 'modResource', '', ['menuindex'], true));
        $c->select($this->modx->getSelectColumns(CollectionSelection::class, 'CollectionSelection', '', ['menuindex']));
        $c->select($this->modx->getSelectColumns(CollectionSelection::class, 'CollectionSelection', 'cs_', ['collection']));

        foreach ($this->tvColumns as $column) {
            $c->select([
                '`' . $column['column'] . '`' => 'IF(`TemplateVarResources_' . $column['column'] . '`.`value` IS NULL OR`TemplateVarResources_' . $column['column'] . '`.`value` = \'\', "' . $column['default'] . '", `TemplateVarResources_' . $column['column'] . '`.`value`)'
            ]);
        }

        $taggerInstalled = $this->collections->getOption('taggerInstalled', null, false);
        if ($taggerInstalled) {
            foreach ($this->taggerColumns as $column) {
                $c->select([
                    '`' . $column . '`' => '(SELECT group_concat(t.tag SEPARATOR \', \') FROM ' . $this->modx->getTableName('Tagger\\Model\\TaggerTagResource') . ' tr LEFT JOIN ' . $this->modx->getTableName('Tagger\\Model\\TaggerTag') . ' t ON t.id = tr.tag LEFT JOIN ' . $this->modx->getTableName('Tagger\\Model\\TaggerGroup') . ' tg ON tg.id = t.group WHERE tr.resource = modResource.id AND tg.alias = \'' . preg_replace('/tagger_/', '', $column, 1) . '\' group by t.group)'
                ]);
            }
        }

        return $c;
    }

    protected function permanentSort(xPDOQuery $c, $gridSort, $sortOptions)
    {
        $sorts = explode(',', $sortOptions);
        foreach ($sorts as $sort) {
            $sort = explode('=', $sort);
            if (isset($sort[1])) {
                if (($sort[0] != '*') && (strtolower($sort[0]) != strtolower($gridSort))) continue;
            }

            $options = (isset($sort[1])) ? $sort[1] : $sort[0];
            $options = explode(':', $options);
            if (empty($options[0])) continue;

            $options['field'] = (strtolower($options[0]) == 'menuindex') ? 'CollectionSelection`.`menuindex' : $options[0];
            $options['dir'] = empty($options[1]) ? $this->getProperty('dir') : $options[1];
            $options['type'] = empty($options[2]) ? null : $options[2];

            if (empty($options['type'])) {
                $c->sortby('`' . $options['field'] . '`', $options['dir']);
            } else {
                $c->sortby('CAST(`' . $options['field'] . '` as ' . $options['type'] . ')', $options['dir']);
            }
        }

        return $c;
    }

    public function iterateWithRenderer(array $data)
    {
        $list = [];
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

    public function prepareRowWithRenderer(xPDOObject $object)
    {
        $resourceArray = parent::prepareRow($object);

        foreach ($this->columnRenderer as $field => $snippet) {
            $value = isset($resourceArray[$field]) ? $resourceArray[$field] : null;
            $resourceArray[$field] = $this->modx->runSnippet($snippet, ['value' => $value, 'row' => $resourceArray, 'input' => $value, 'column' => $field]);
        }

        $resourceArray = $this->prepareSupportFields($resourceArray);
        $resourceArray = $this->prepareActions($resourceArray);
        $resourceArray = $this->prepareMenuActions($resourceArray);

        return $resourceArray;
    }

    public function prepareSupportFields($resourceArray)
    {
        $version = $this->modx->getVersionData();

        $parent = (int)$this->getProperty('parent', null);

        if ($version['major_version'] < 3) {
            $resourceArray['action_edit'] = '?a=30&id=' . $resourceArray['id'] . '&selection=' . $parent;
        } else {
            $resourceArray['action_edit'] = '?a=resource/update&action=post/update&id=' . $resourceArray['id'] . '&selection=' . $parent;
        }

        $this->modx->getContext($resourceArray['context_key']);

        if (!$resourceArray['deleted']) {
            $resourceArray['preview_url'] = $this->modx->makeUrl($resourceArray['id'], $resourceArray['context_key']);
        } else {
            $resourceArray['preview_url'] = '';
        }

        return $resourceArray;
    }

    public function prepareActions($resourceArray)
    {
        $resourceArray['actions'] = [];

        foreach ($this->buttons as $button) {
            if (isset($this->actions[$button])) {
                switch ($button) {
                    case 'publish':
                        if ($this->permissions['publish_document'] && empty($resourceArray['published'])) {
                            $resourceArray['actions'][] = $this->actions[$button];
                        }
                        break;
                    case 'unpublish':
                        if ($this->permissions['unpublish_document'] && !empty($resourceArray['published'])) {
                            $resourceArray['actions'][] = $this->actions[$button];
                        }
                        break;
                    case 'quickupdate':
                        if ($this->permissions['edit_document']) {
                            $resourceArray['actions'][] = $this->actions[$button];
                        }
                        break;
                    case 'delete':
                        if ($this->permissions['delete_document'] && empty($resourceArray['deleted'])) {
                            $resourceArray['actions'][] = $this->actions[$button];
                        }
                        break;
                    case 'undelete':
                        if ($this->permissions['undelete_document'] && !empty($resourceArray['deleted'])) {
                            $resourceArray['actions'][] = $this->actions[$button];
                        }
                        break;
                    case 'remove':
                        if ($this->permissions['purge_deleted'] && !empty($resourceArray['deleted'])) {
                            $resourceArray['actions'][] = $this->actions[$button];
                        }
                        break;
                    case 'edit':
                        if ($this->permissions['edit_document']) {
                            $resourceArray['actions'][] = $this->actions[$button];
                        }
                        break;
                    case 'view':
                        if ($this->permissions['view_document'] && !$resourceArray['deleted']) {
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

    public function prepareMenuActions($resourceArray)
    {
        $resourceArray['menu_actions'] = [];

        if ($this->permissions['view_document'] && !$resourceArray['deleted']) {
            $resourceArray['menu_actions']['view'] = $this->actions['view'];
        }

        if ($this->permissions['edit_document']) {
            $resourceArray['menu_actions']['edit'] = $this->actions['edit'];
            $resourceArray['menu_actions']['quickupdate'] = $this->actions['quickupdate'];
        }

        if (!empty($resourceArray['published'])) {
            if ($this->permissions['unpublish_document']) {
                $resourceArray['menu_actions']['unpublish'] = $this->actions['unpublish'];
            }
        } else {
            if ($this->permissions['publish_document']) {
                $resourceArray['menu_actions']['publish'] = $this->actions['publish'];
            }
        }

        if (!empty($resourceArray['deleted'])) {
            if ($this->permissions['undelete_document']) {
                $resourceArray['menu_actions']['undelete'] = $this->actions['undelete'];
            }

            if ($this->permissions['purge_deleted']) {
                $resourceArray['menu_actions']['remove'] = $this->actions['remove'];
            }
        } else {
            if ($this->permissions['delete_document']) {
                $resourceArray['menu_actions']['delete'] = $this->actions['delete'];
            }
        }

        $resourceArray['menu_actions']['unlink'] = $this->actions['unlink'];

        return $resourceArray;
    }

    public function iterate(array $data)
    {
        $list = [];
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

    /**
     * @param xPDOObject $object
     * @return array
     */
    public function prepareRow(xPDOObject $object)
    {
        $resourceArray = parent::prepareRow($object);

        $resourceArray = $this->prepareSupportFields($resourceArray);
        $resourceArray = $this->prepareActions($resourceArray);
        $resourceArray = $this->prepareMenuActions($resourceArray);

        return $resourceArray;
    }
}
