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

    /** @var modAction $editAction */
    public $editAction;
    /** @var boolean $commentsEnabled */
    public $commentsEnabled = false;

    public function initialize() {
        $this->editAction = $this->modx->getObject('modAction',array(
            'namespace' => 'core',
            'controller' => 'resource/update',
        ));

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
            $queryWhere = array(
                'pagetitle:LIKE' => '%'.$query.'%',
                'OR:description:LIKE' => '%'.$query.'%',
                'OR:introtext:LIKE' => '%'.$query.'%',
            );
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
            $this->modx->log(modX::LOG_LEVEL_ERROR, '[' . $elementname . '] publishedon tsamp ' . $publishedon);
            $resourceArray['publishedon_date'] = strftime($this->modx->getOption('collections.mgr_date_format',null,'%b %d'),$publishedon);
            $resourceArray['publishedon_time'] = strftime($this->modx->getOption('collections.mgr_time_format',null,'%H:%M %p'),$publishedon);
            $resourceArray['publishedon'] = strftime('%b %d, %Y %H:%I %p',$publishedon);
        }

        if (!empty($resourceArray['unpub_date'])) {
            $unpublishon = strtotime($resourceArray['unpub_date']);
            $resourceArray['unpublishon_date'] = strftime($this->modx->getOption('collections.mgr_date_format',null,'%b %d'),$unpublishon);
            $resourceArray['unpublishon_time'] = strftime($this->modx->getOption('collections.mgr_time_format',null,'%H:%M %p'),$unpublishon);
            $resourceArray['unpublishon'] = strftime('%b %d, %Y %H:%I %p',$unpublishon);
        }

        $resourceArray['action_edit'] = '?a='.$this->editAction->get('id').'&action=post/update&id='.$resourceArray['id'];

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