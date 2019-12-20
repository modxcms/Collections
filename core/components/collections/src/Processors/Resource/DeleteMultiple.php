<?php
namespace Collections\Processors\Resource;
use MODX\Revolution\modResource;
use MODX\Revolution\Processors\ModelProcessor;

class DeleteMultiple extends ModelProcessor
{
    public $classKey = modResource::class;
    public $languageTopics = ['resource', 'collections:default'];

    public function process()
    {
        $ids = $this->getProperty('ids', null);
        if (empty($ids)) {
            return $this->failure($this->modx->lexicon('collections.children.err_ns_multiple'));
        }
        $ids = is_array($ids) ? $ids : explode(',', $ids);

        $lastId = 0;

        foreach ($ids as $id) {
            if (empty($id)) continue;
            $lastId = $id;
            $this->modx->runProcessor('Collections\\Processors\\Resource\\Delete', [
                'id' => $id,
                'skipClearCache' => true,
            ]);
        }

        /** @var modResource $res */
        $res = $this->modx->getObject(modResource::class, $lastId);

        if ($res) {
            $this->clearCache($res->context_key);
        }

        return $this->success();
    }

    public function clearCache($context)
    {
        $this->modx->cacheManager->refresh([
            'db' => [],
            'auto_publish' => ['contexts' => [$context]],
            'context_settings' => ['contexts' => [$context]],
            'resource' => ['contexts' => [$context]],
        ]);
    }
}
