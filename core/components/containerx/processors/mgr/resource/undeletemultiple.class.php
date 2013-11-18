<?php
/**
 * Undelete multiple children
 *
 * @package containerx
 * @subpackage processors.resource
 */
class ContainerXUnDeleteMultipleProcessor extends modObjectProcessor {
    public $classKey = 'modResource';
    public $languageTopics = array('resource','containerx:default');

    public function process() {
        $ids = $this->getProperty('ids',null);
        if (empty($ids)) {
            return $this->failure($this->modx->lexicon('articles.articles_err_ns_multiple'));
        }
        $ids = is_array($ids) ? $ids : explode(',',$ids);

        foreach ($ids as $id) {
            if (empty($id)) continue;
            $this->modx->runProcessor('resource/undelete',array(
                'id' => $id,
            ));
        }
        return $this->success();
    }
}
return 'ContainerXUnDeleteMultipleProcessor';