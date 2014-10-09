<?php
require_once dirname(dirname(__FILE__)) . '/update.class.php';

/**
 * Update controller for Collections Container
 *
 * @package collections
 * @subpackage controller
 */
class SelectionContainerUpdateManagerController extends CollectionContainerUpdateManagerController {
    /**
     * Register custom CSS/JS for the page
     * @return void
     */
    public function loadCustomCssJs() {
        $managerUrl = $this->context->getOption('manager_url', MODX_MANAGER_URL, $this->modx->_userConfig);
        $collectionsAssetsUrl = $this->modx->getOption('collections.assets_url',null,$this->modx->getOption('assets_url',null,MODX_ASSETS_URL).'components/collections/');
        $connectorUrl = $collectionsAssetsUrl.'connector.php';
        $collectionsJsUrl = $collectionsAssetsUrl.'js/mgr/';

        $this->addCss($collectionsAssetsUrl . 'css/mgr.css');

        $userCSS = $this->modx->getOption('collections.user_css', '');
        if ($userCSS != '') {
            $this->addCss($userCSS);
        }

        $this->addJavascript($managerUrl.'assets/modext/util/datetime.js');
        $this->addJavascript($managerUrl.'assets/modext/widgets/element/modx.panel.tv.renders.js');
        $this->addJavascript($managerUrl.'assets/modext/widgets/resource/modx.grid.resource.security.local.js');
        $this->addJavascript($managerUrl.'assets/modext/widgets/resource/modx.panel.resource.tv.js');
        $this->addJavascript($managerUrl.'assets/modext/widgets/resource/modx.panel.resource.js');
        $this->addJavascript($managerUrl.'assets/modext/sections/resource/update.js');

        $this->addJavascript($collectionsJsUrl.'collections.js');
        $this->addLastJavascript($collectionsJsUrl.'sections/category/update.js');
        $this->addLastJavascript($collectionsJsUrl.'widgets/category/collections.panel.category.js');
        $this->addLastJavascript($collectionsJsUrl.'widgets/category/collections.panel.selection.js');
        $this->addLastJavascript($collectionsJsUrl.'widgets/category/collections.grid.resources.js');
        $this->addLastJavascript($collectionsJsUrl.'widgets/category/collections.grid.selection.js');
        $this->addLastJavascript($collectionsJsUrl.'widgets/category/collections.window.js');
        $this->addLastJavascript($collectionsJsUrl.'extra/collections.combo.js');
        $this->addLastJavascript($collectionsJsUrl.'extra/griddraganddrop.js');
        $this->addLastJavascript($collectionsJsUrl.'extra/collections.renderers.js');

        $userJS = $this->modx->getOption('collections.user_js', '');
        if ($userJS != '') {
            $this->addLastJavascript($userJS);
        }

        $collectionsTemplate = $this->getCollectionsTemplate();

        $response = $this->modx->runProcessor('system/derivatives/getlist', array(
            'skip' => 'modXMLRPCResource',
            'class' => 'modResource',
        ));

        $response = $this->modx->fromJSON($response->response);
        if ($response == '') {
            $response = array();
        } else {
            $response = $response['results'];
        }

        $this->loadConfig();

        $this->addHtml('
        <script type="text/javascript">
        // <![CDATA[
        Collections.assetsUrl = "'.$collectionsAssetsUrl.'";
        Collections.connectorUrl = "'.$connectorUrl.'";
        Collections.config = '.$this->modx->toJSON($this->modx->collections->config).';
        Collections.config.connector_url = "'.$this->modx->collections->config['connectorUrl'].'";
        MODx.config.publish_document = "'.$this->canPublish.'";
        MODx.onDocFormRender = "'.$this->onDocFormRender.'";
        MODx.ctx = "'.$this->resource->get('context_key').'";
        Collections.template = ' . $collectionsTemplate . ';
        Collections.resourceDerivatives = ' . $this->modx->toJSON($response) . ';
        Ext.onReady(function() {
            MODx.load({
                xtype: "collections-page-selection-update"
                ,resource: "'.$this->resource->get('id').'"
                ,record: '.$this->modx->toJSON($this->resourceArray).'
                ,publish_document: "'.$this->canPublish.'"
                ,preview_url: "'.$this->previewUrl.'"
                ,locked: '.($this->locked ? 1 : 0).'
                ,lockedText: "'.$this->lockedText.'"
                ,canSave: '.($this->canSave ? 1 : 0).'
                ,canEdit: '.($this->canEdit ? 1 : 0).'
                ,canCreate: '.($this->canCreate ? 1 : 0).'
                ,canDuplicate: '.($this->canDuplicate ? 1 : 0).'
                ,canDelete: '.($this->canDelete ? 1 : 0).'
                ,show_tvs: '.(!empty($this->tvCounts) ? 1 : 0).'
                ,mode: "update"
            });
        });
        // ]]>
        </script>');
        /* load RTE */
        $this->loadRichTextEditor();
    }
}