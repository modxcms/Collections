<?php
/**
 * Get list Templates
 *
 * @package collections
 * @subpackage processors
 */
class CollectionsTemplateGetListProcessor extends modObjectGetListProcessor {
    public $classKey = 'CollectionTemplate';
    public $languageTopics = array('collections:default');
    public $defaultSortField = 'name';
    public $defaultSortDirection = 'ASC';
    public $objectType = 'collections.template';

    public function beforeIteration(array $list) {
        $addEmpty = $this->getProperty('addEmpty', false);

        if ($addEmpty) {
            $list[] = array(
                'id' => 0,
                'name' => $this->modx->lexicon('collections.template.empty'),
            );
        }
        return $list;
    }
}
return 'CollectionsTemplateGetListProcessor';