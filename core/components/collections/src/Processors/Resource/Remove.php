<?php
namespace Collections\Processors\Resource;
use Collections\Model\CollectionSelection;
use MODX\Revolution\modResource;
use MODX\Revolution\modResourceGroupResource;
use MODX\Revolution\modTemplateVarResource;
use MODX\Revolution\modUser;
use MODX\Revolution\Processors\Processor;

class Remove extends Processor
{
    /** @var modResource $resource */
    public $resource;
    /** @var modUser $lockedUser */
    public $lockedUser;
    /** @var array $children */
    public $children = [];
    /** @var int $deletedTime */
    public $deletedTime = 0;

    public function checkPermissions()
    {
        return $this->modx->hasPermission('purge_deleted');
    }

    public function getLanguageTopics()
    {
        return ['resource'];
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
        $this->resource = $this->modx->getObject(modResource::class, $id);
        if (empty($this->resource)) return $this->modx->lexicon('resource_err_nfs', ['id' => $id]);

        /* validate resource can be deleted */
        if (!$this->resource->checkPolicy(['save' => true, 'delete' => true])) {
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

        $resources = [$this->resource];
        $ids = [$this->resource->id];

        $this->modx->invokeEvent('OnBeforeEmptyTrash', [
            'ids' => &$ids,
            'resources' => &$resources,
        ]);

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

        $this->modx->removeCollection(CollectionSelection::class, ['resource' => $this->resource->id]);

        if ($this->resource->remove() == false) {
            return $this->failure($this->modx->lexicon('resource_err_delete'));
        }

        $this->modx->invokeEvent('OnEmptyTrash', [
            'num_deleted' => 1,
            'resources' => &$resources,
            'ids' => &$ids,
        ]);

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
        $this->modx->cacheManager->refresh([
            'db' => [],
            'auto_publish' => ['contexts' => [$this->resource->get('context_key')]],
            'context_settings' => ['contexts' => [$this->resource->get('context_key')]],
            'resource' => ['contexts' => [$this->resource->get('context_key')]],
        ]);
    }
}
