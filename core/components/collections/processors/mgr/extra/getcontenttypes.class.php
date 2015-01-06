<?php

class CollectionsContentTypeGetListProcessor extends modObjectGetListProcessor {
    public $classKey = 'modContentType';
    public $languageTopics = array('content_type');

    public function afterIteration(array $list) {
        array_unshift($list, array('id' => 0, 'name' => $this->modx->lexicon('collections.global.use_default')));

        return $list;
    }
}

return 'CollectionsContentTypeGetListProcessor';