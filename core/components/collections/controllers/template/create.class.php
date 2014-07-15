<?php
require_once dirname(dirname(dirname(__FILE__))) . '/index.class.php';

/**
 * Create controller for Collections Container
 *
 * @package collections
 * @subpackage controller
 */
class CollectionsTemplateCreateManagerController extends CollectionsBaseManagerController {
    public function getLanguageTopics() {
        return array('collections:default');
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
        $this->addLastJavascript($this->collections->getOption('jsUrl').'mgr/extra/collections.combo.js');

        $this->addLastJavascript($this->collections->getOption('jsUrl').'mgr/widgets/template/template.panel.js');
        $this->addLastJavascript($this->collections->getOption('jsUrl').'mgr/sections/template/template.js');
    }

    public function getTemplateFile() { return $this->collections->getOption('templatesPath').'template/template.tpl'; }

}