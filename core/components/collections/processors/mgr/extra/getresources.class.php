<?php
/**
 * Get list of Children
 *
 * @package collections
 * @subpackage processors.resource
 */
class CollectionsExtrasResourceGetListProcessor extends modObjectGetListProcessor {
    public $classKey = 'modResource';
    public $defaultSortField = 'pagetitle';
    public $defaultSortDirection = 'asc';
    public $checkListPermission = true;
    public $languageTopics = array('resource','collections:default', 'collections:selections');

    public function prepareQueryBeforeCount(xPDOQuery $c) {
        $query = $this->getProperty('query');
        if (!empty($query)) {
            $c->where(array(
                'pagetitle:LIKE' => '%'.$query.'%'
            ));
        }

        return $c;
    }

}
return 'CollectionsExtrasResourceGetListProcessor';