<?php
class CollectionsContainerCreateManagerController extends ResourceCreateManagerController {
    public function getLanguageTopics() {
        return array('resource','collections:default');
    }

    /**
     * Return the pagetitle
     *
     * @return string
     */
    public function getPageTitle() {
        return $this->modx->lexicon('collections.system.new_category');
    }

}