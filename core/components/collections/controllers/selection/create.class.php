<?php
require_once dirname(dirname(__FILE__)) . '/create.class.php';

/**
 * Create controller for Collections Container
 *
 * @package collections
 * @subpackage controller
 */
class SelectionContainerCreateManagerController extends CollectionContainerCreateManagerController {
    /**
     * Return the pagetitle
     *
     * @return string
     */
    public function getPageTitle() {
        return $this->modx->lexicon('selections.system.new_container');
    }
}