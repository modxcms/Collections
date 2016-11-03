<?php

/**
 * Create controller for Collections Container
 *
 * @package collections
 * @subpackage controller
 */
class CollectionContainerCreateManagerController extends ResourceCreateManagerController
{
    public function getLanguageTopics()
    {
        return array('resource', 'collections:default', 'collections:selections');
    }

    /**
     * Return the pagetitle
     *
     * @return string
     */
    public function getPageTitle()
    {
        return $this->modx->lexicon('collections.system.new_container');
    }

}