<?php
/**
 * Delete multiple children
 *
 * @package collections
 * @subpackage processors.resource
 */

class CollectionsDeleteMultipleProcessor extends modObjectProcessor {
    public $classKey = 'modResource';
    public $languageTopics = array('resource','collections:default');

    public function process() {
        $ids = $this->getProperty('ids',null);
        if (empty($ids)) {
            return $this->failure($this->modx->lexicon('collections.children.err_ns_multiple'));
        }
        $ids = is_array($ids) ? $ids : explode(',',$ids);

        foreach ($ids as $id) {
            if (empty($id)) continue;
            $this->modx->runProcessor('resource/delete',array(
                'id' => $id,
            ));
        }
        return $this->success();
    }
}
return 'CollectionsDeleteMultipleProcessor';