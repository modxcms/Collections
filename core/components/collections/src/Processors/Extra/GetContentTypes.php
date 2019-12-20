<?php
namespace Collections\Processors\Extra;

use MODX\Revolution\modContentType;
use MODX\Revolution\Processors\Model\GetListProcessor;

class GetContentTypes extends GetListProcessor
{
    public $classKey = modContentType::class;
    public $languageTopics = ['content_type'];

    public function afterIteration(array $list)
    {
        array_unshift($list, ['id' => 0, 'name' => $this->modx->lexicon('collections.global.use_default')]);

        return $list;
    }
}
