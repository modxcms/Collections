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

    public function initialize() {
        $parent = $this->getProperty('parent',null);
        if (empty($parent)) {
            return false;
        }

        $template = null;

        /** @var CollectionSetting $collectionSetting */
        $collectionSetting = $this->modx->getObject('CollectionSetting', array('collection' => $parent));
        if ($collectionSetting) {
            if (intval($collectionSetting->template) > 0) {
                $template = $collectionSetting->Template;
            }
        }

        if ($template == null) {
            /** @var CollectionResourceTemplate $resourceTemplate */
            $resourceTemplate = $this->modx->getObject('CollectionResourceTemplate', array('resource_template' => $parent));
            if ($resourceTemplate) {
                $template = $resourceTemplate->CollectionTemplate;
            } else {
                $template = $this->modx->getObject('CollectionTemplate', array('global_template' => 1));
            }
        }

        $templateColumnsQuery = $this->modx->newQuery('CollectionTemplateColumn');
        $templateColumnsQuery->where(array(
            'template' => $template->id,
        ));
        $templateColumnsQuery->where(array(
            'name:LIKE' => 'tv_%',
            'OR:name:LIKE' => 'tagger_%',
        ));
        $templateColumnsQuery->select($this->modx->getSelectColumns('CollectionTemplateColumn', '', '', array('name')));
        $templateColumnsQuery->prepare();
        $templateColumnsQuery->stmt->execute();

        $columns = $templateColumnsQuery->stmt->fetchAll(PDO::FETCH_COLUMN, 0);

        foreach ($columns as $column) {
            if (strpos($column, 'tv_') !== false) {
                $this->tvColumns[] = $column;
            } else {
                $this->taggerColumns[] = $column;
            }
        }

        return parent::initialize();
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
                'OR:introtext:LIKE' => '%'.$query.'%',
                'OR:CreatedByProfile.fullname:LIKE' => '%'.$query.'%',
                'OR:CreatedBy.username:LIKE' => '%'.$query.'%',
            );

            $taggerInstalled = $this->modx->collections->getOption('taggerInstalled', null,  false);
            if ($taggerInstalled) {
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
            $name = str_replace('tv_', '', $column);

            $c->leftJoin('modTemplateVarResource', 'TemplateVarResources_' . $column, 'TemplateVarResources_'.$column.'.contentid = modResource.id');
            $c->leftJoin('modTemplateVar', 'TemplateVar_' . $column, 'TemplateVar_'.$column.'.id = TemplateVarResources_'.$column.'.tmplvarid AND TemplateVar_'.$column.'.name = \'' . $name .'\'');
        }

        return $c;
    }

    public function prepareQueryAfterCount(xPDOQuery $c) {

        $c->select($this->modx->getSelectColumns('modResource', 'modResource'));

        foreach ($this->tvColumns as $column) {
            $c->select(array(
                $column => 'TemplateVarResources_' . $column . '.value'
            ));
        }

        $taggerInstalled = $this->modx->collections->getOption('taggerInstalled', null,  false);
        if ($taggerInstalled) {
            foreach ($this->taggerColumns as $column) {
                $c->select(array(
                    $column => '(SELECT group_concat(t.tag SEPARATOR \', \') FROM `modx_tagger_tag_resources` tr LEFT JOIN `modx_tagger_tags` t ON t.id = tr.tag LEFT JOIN `modx_tagger_groups` tg ON tg.id = t.group WHERE tr.resource = modResource.id AND tg.alias = \'' . str_replace('tagger_', '', $column) . '\' group by t.group)'
                ));
            }
        }

        return $c;
    }

    /**
     * @param modResource $object
     * @return array
     */
    public function prepareRow($object) {
        $resourceArray = parent::prepareRow($object);

        $resourceArray['action_edit'] = '?a=resource/update&action=post/update&id='.$resourceArray['id'];

        $this->modx->getContext($resourceArray['context_key']);
        $resourceArray['preview_url'] = $this->modx->makeUrl($resourceArray['id'],$resourceArray['context_key']);

        $resourceArray['actions'] = array();
        $resourceArray['actions'][] = array(
            'className' => 'edit',
            'text' => $this->modx->lexicon('edit'),
        );
        $resourceArray['actions'][] = array(
            'className' => 'duplicate',
            'text' => $this->modx->lexicon('duplicate'),
        );
        $resourceArray['actions'][] = array(
            'className' => 'view',
            'text' => $this->modx->lexicon('view'),
        );
        if (!empty($resourceArray['deleted'])) {
            $resourceArray['actions'][] = array(
                'className' => 'undelete',
                'text' => $this->modx->lexicon('undelete'),
            );
        } else {
            $resourceArray['actions'][] = array(
                'className' => 'delete',
                'text' => $this->modx->lexicon('delete'),
            );
        }
        if (!empty($resourceArray['published'])) {
            $resourceArray['actions'][] = array(
                'className' => 'unpublish',
                'text' => $this->modx->lexicon('unpublish'),
            );
        } else {
            $resourceArray['actions'][] = array(
                'className' => 'publish orange',
                'text' => $this->modx->lexicon('publish'),
            );
        }
        return $resourceArray;
    }
}
return 'CollectionsResourceGetListProcessor';