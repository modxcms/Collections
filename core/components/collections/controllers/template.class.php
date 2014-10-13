<?php
require_once dirname(dirname(__FILE__)) . '/index.class.php';

/**
 * Create controller for Collections Container
 *
 * @package collections
 * @subpackage controller
 */
class CollectionsTemplateManagerController extends CollectionsBaseManagerController {
    public function getLanguageTopics() {
        return array('collections:default', 'collections:selections');
    }

    /**
     * Return the pagetitle
     *
     * @return string
     */
    public function getPageTitle() {
        return $this->modx->lexicon('collections.template.page_title');
    }

    public function process(array $scriptProperties = array()) {}

    public function loadCustomCssJs() {
        $this->addLastJavascript($this->collections->getOption('jsUrl').'mgr/widgets/template/template.window.js');
        $this->addLastJavascript($this->collections->getOption('jsUrl').'mgr/widgets/template/template.grid.js');
        $this->addLastJavascript($this->collections->getOption('jsUrl').'mgr/widgets/template/home.panel.js');
        $this->addLastJavascript($this->collections->getOption('jsUrl').'mgr/sections/template/home.js');
    }

    public function getTemplateFile() { return $this->collections->getOption('templatesPath').'template/home.tpl'; }

}