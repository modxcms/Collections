<?php
require_once dirname(dirname(dirname(__FILE__))) . '/index.class.php';

/**
 * Update controller for Collections Container
 *
 * @package collections
 * @subpackage controller
 */
class CollectionsTemplateUpdateManagerController extends CollectionsBaseManagerController {
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
        $this->addLastJavascript($this->collections->getOption('jsUrl').'mgr/extra/griddraganddrop.js');
        $this->addLastJavascript($this->collections->getOption('jsUrl').'mgr/extra/collections.renderers.js');
        $this->addLastJavascript($this->collections->getOption('jsUrl').'mgr/extra/collections.combo.js');
        $this->addLastJavascript($this->collections->getOption('jsUrl').'mgr/widgets/template/column.window.js');
        $this->addLastJavascript($this->collections->getOption('jsUrl').'mgr/widgets/template/column.grid.js');
        $this->addLastJavascript($this->collections->getOption('jsUrl').'mgr/widgets/template/template.panel.js');
        $this->addLastJavascript($this->collections->getOption('jsUrl').'mgr/sections/template/template.js');

        $this->addHtml('
        <script type="text/javascript">
            Ext.onReady(function() {
                MODx.load({ xtype: "collections-page-template"});
            });
        </script>
        ');
    }

    public function getTemplateFile() { return $this->collections->getOption('templatesPath').'template/template.tpl'; }

}