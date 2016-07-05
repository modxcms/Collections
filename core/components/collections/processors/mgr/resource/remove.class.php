<?php

/**
 * Remove a resource.
 *
 * @param integer $id The ID of the resource
 *
 * @package modx
 * @subpackage processors.resource
 */
class CollectionsResourceRemoveProcessor extends modProcessor
{
    /** @var modResource $resource */
    public $resource;
    /** @var modUser $lockedUser */
    public $lockedUser;
    /** @var array $children */
    public $children = array();
    /** @var int $deletedTime */
    public $deletedTime = 0;

    public function checkPermissions()
    {
        return $this->modx->hasPermission('delete_document');
    }

    public function getLanguageTopics()
    {
        return array('resource');
    }

    /**
     * Get the Resource and check for proper permissions
     *
     * {@inheritDoc}
     * @return boolean|string
     */
    public function initialize()
    {
        $id = $this->getProperty('id', false);
        if (empty($id)) return $this->modx->lexicon('resource_err_ns');
        $this->resource = $this->modx->getObject('modResource', $id);
        if (empty($this->resource)) return $this->modx->lexicon('resource_err_nfs', array('id' => $id));

        /* validate resource can be deleted */
        if (!$this->resource->checkPolicy(array('save' => true, 'delete' => true))) {
            return $this->modx->lexicon('permission_denied');
        }
        $this->deletedTime = time();
        return true;
    }

    /**
     * {@inheritDoc}
     * @return mixed
     */
    public function process()
    {

        if (!$this->resource->checkPolicy('delete')) return $this->failure($this->modx->lexicon('resource_err_delete'));

        $resources = array($this->resource);
        $ids = array($this->resource->id);

        $this->modx->invokeEvent('OnBeforeEmptyTrash', array(
            'ids' => &$ids,
            'resources' => &$resources,
        ));

        $this->handleChildren();

        /** @var modResourceGroupResource[] $resourceGroupResources */
        $resourceGroupResources = $this->resource->getMany('ResourceGroupResources');
        /** @var modTemplateVarResource[] $templateVarResources */
        $templateVarResources = $this->resource->getMany('TemplateVarResources');

        foreach ($resourceGroupResources as $resourceGroupResource) {
            $resourceGroupResource->remove();
        }

        foreach ($templateVarResources as $templateVarResource) {
            $templateVarResource->remove();
        }

        $this->modx->removeCollection('CollectionSelection', array('resource' => $this->resource->id));

        if ($this->resource->remove() == false) {
            return $this->failure($this->modx->lexicon('resource_err_delete'));
        }

        $this->modx->invokeEvent('OnEmptyTrash', array(
            'num_deleted' => 1,
            'resources' => &$resources,
            'ids' => &$ids,
        ));

        $this->logManagerAction();

        $skipClearCache = $this->getProperty('skipClearCache', false);
        if ($skipClearCache == false) {
            $this->clearCache();
        }

        return $this->success();
    }

    public function handleChildren()
    {
        /** @var modResource[] $children */
        $children = $this->resource->Children;

        foreach ($children as $child) {
            $child->set('parent', $this->resource->parent);
            $child->save();
        }
    }

    /**
     * Log the manager action
     *
     * @return void
     */
    public function logManagerAction()
    {
        $this->modx->logManagerAction('remove_resource', $this->resource->get('class_key'), $this->resource->get('id'));
    }

    /**
     * Clear the site cache
     * @return void
     */
    public function clearCache()
    {
        $this->modx->cacheManager->refresh(array(
            'db' => array(),
            'auto_publish' => array('contexts' => array($this->resource->get('context_key'))),
            'context_settings' => array('contexts' => array($this->resource->get('context_key'))),
            'resource' => array('contexts' => array($this->resource->get('context_key'))),
        ));
    }
}

return 'CollectionsResourceRemoveProcessor';