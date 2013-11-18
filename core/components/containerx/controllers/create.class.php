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

    /**
     * Register custom CSS/JS for the page
     * @return void
     */
//    public function loadCustomCssJs() {
//        $mgrUrl = $this->modx->getOption('manager_url',null,MODX_MANAGER_URL);
//        $containerxAssetsUrl = $this->modx->getOption('containerx.assets_url',null,$this->modx->getOption('assets_url',null,MODX_ASSETS_URL).'components/containerx/');
//        $connectorUrl = $containerxAssetsUrl.'connector.php';
//        $containerxJsUrl = $containerxAssetsUrl.'js/mgr/';
//
//        $this->addJavascript($mgrUrl.'assets/modext/util/datetime.js');
//        $this->addJavascript($mgrUrl.'assets/modext/widgets/element/modx.panel.tv.renders.js');
//        $this->addJavascript($mgrUrl.'assets/modext/widgets/resource/modx.grid.resource.security.local.js');
//        $this->addJavascript($mgrUrl.'assets/modext/widgets/resource/modx.panel.resource.tv.js');
//        $this->addJavascript($mgrUrl.'assets/modext/widgets/resource/modx.panel.resource.js');
//        $this->addJavascript($mgrUrl.'assets/modext/sections/resource/create.js');
//
//        $this->addJavascript($containerxJsUrl.'containerx.js');
//        $this->addLastJavascript($containerxJsUrl.'sections/category/create.js');
//        $this->addLastJavascript($containerxJsUrl.'widgets/category/containerx.panel.category.js');
//
//        $this->addHtml('
//        <script type="text/javascript">
//        // <![CDATA[
//        ContainerX.assetsUrl = "'.$containerxAssetsUrl.'";
//        ContainerX.connectorUrl = "'.$connectorUrl.'";
//        MODx.config.publish_document = "'.$this->canPublish.'";
//        MODx.onDocFormRender = "'.$this->onDocFormRender.'";
//        MODx.ctx = "'.$this->ctx.'";
//        Ext.onReady(function() {
//            MODx.load({
//                xtype: "containerx-page-category-create"
//                ,record: '.$this->modx->toJSON($this->resourceArray).'
//                ,publish_document: "'.$this->canPublish.'"
//                ,canSave: "'.($this->modx->hasPermission('save_document') ? 1 : 0).'"
//                ,show_tvs: '.(!empty($this->tvCounts) ? 1 : 0).'
//                ,mode: "create"
//            });
//        });
//        // ]]>
//        </script>');
//        /* load RTE */
//        $this->loadRichTextEditor();
//    }
}