<?php
/**
 * Publish multiple children
 *
 * @package collections
 * @subpackage processors.resource
 */
class CollectionsPublishMultipleProcessor extends modObjectProcessor {
    public $classKey = 'modResource';
    public $languageTopics = array('resource','collections:default');

    public function process() {
        $ids = $this->getProperty('ids',null);
        if (empty($ids)) {
            return $this->failure($this->modx->lexicon('collections.children.err_ns_multiple'));
        }
        $ids = is_array($ids) ? $ids : explode(',',$ids);

        $corePath = $this->modx->getOption('collections.core_path',null,$this->modx->getOption('core_path').'components/collections/');
        $path = $this->modx->getOption('processorsPath',$this->modx->collections->config,$corePath.'processors/');

        $lastId = 0;

        foreach ($ids as $id) {
            if (empty($id)) continue;
            $lastId = $id;
            $this->modx->runProcessor('mgr/resource/publish',array(
                'id' => $id,
                'skipClearCache' => true,
            ), array(
                'processors_path' => $path,
            ));
        }

        /** @var modResource $res */
        $res = $this->modx->getObject('modResource', $lastId);

        if ($res) {
            $this->clearCache($res->context_key);
        }

        return $this->success();
    }

    public function clearCache($context) {
        $this->modx->cacheManager->refresh(array(
            'db' => array(),
            'auto_publish' => array('contexts' => array($context)),
            'context_settings' => array('contexts' => array($context)),
            'resource' => array('contexts' => array($context)),
        ));
    }
}
return 'CollectionsPublishMultipleProcessor';