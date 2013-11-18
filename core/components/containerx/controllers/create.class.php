<?php
class ContainerXItemCreateManagerController extends ResourceCreateManagerController {
    public function getLanguageTopics() {
        return array('resource','containerx:default');
    }

    /**
     * Return the pagetitle
     *
     * @return string
     */
    public function getPageTitle() {
        return $this->modx->lexicon('containerx.system.new_category');
    }

}