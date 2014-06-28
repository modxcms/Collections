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

    /** @var boolean $commentsEnabled */
    public $commentsEnabled = false;

    public function initialize() {
        $sortBy = $this->getProperty('sort');

        if ($sortBy == 'unpublishedon') {
            $this->setProperty('sort', 'unpub_date');
        }

        return parent::initialize();
    }

    public function prepareQueryBeforeCount(xPDOQuery $c) {
        $parent = $this->getProperty('parent',null);
        if (!empty($parent)) {
            $c->where(array(
                'parent' => $parent,
            ));
        }

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

        return $c;
    }

    /**
     * @param xPDOObject $object
     * @return array
     */
    public function prepareRow(xPDOObject $object) {
        $resourceArray = parent::prepareRow($object);

        if (!empty($resourceArray['publishedon']) || !empty($resourceArray['pub_date'])) {
            $publishedon = strtotime($resourceArray['publishedon']) == '' ? strtotime($resourceArray['pub_date']) : strtotime($resourceArray['publishedon']);
            $resourceArray['publishedon_date'] = strftime($this->modx->getOption('collections.mgr_date_format',null,'%b %d'),$publishedon);
            $resourceArray['publishedon_time'] = strftime($this->modx->getOption('collections.mgr_time_format',null,'%H:%M %p'),$publishedon);
            $resourceArray['publishedon'] = strftime('%b %d, %Y %H:%I %p',$publishedon);
        }

        if (!empty($resourceArray['unpub_date'])) {
            $unpublishon = strtotime($resourceArray['unpub_date']);
            $resourceArray['unpublishedon_date'] = strftime($this->modx->getOption('collections.mgr_date_format',null,'%b %d'),$unpublishon);
            $resourceArray['unpublishedon_time'] = strftime($this->modx->getOption('collections.mgr_time_format',null,'%H:%M %p'),$unpublishon);
            $resourceArray['unpublishedon'] = strftime('%b %d, %Y %H:%I %p',$unpublishon);
        }

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