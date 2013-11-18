<?php
class ContainerXItemUpdateManagerController extends ResourceUpdateManagerController {
    /** @var ContainerXCategory $resource */
    public $resource;

    public function getLanguageTopics() {
        return array('resource','containerx:default');
    }

    /**
     * Register custom CSS/JS for the page
     * @return void
     */
    public function loadCustomCssJs() {
        $managerUrl = $this->context->getOption('manager_url', MODX_MANAGER_URL, $this->modx->_userConfig);
        $containerxAssetsUrl = $this->modx->getOption('containerx.assets_url',null,$this->modx->getOption('assets_url',null,MODX_ASSETS_URL).'components/containerx/');
        $connectorUrl = $containerxAssetsUrl.'connector.php';
        $containerxJsUrl = $containerxAssetsUrl.'js/mgr/';

        $this->addCss($containerxAssetsUrl . 'css/mgr.css');

        $this->addJavascript($managerUrl.'assets/modext/util/datetime.js');
        $this->addJavascript($managerUrl.'assets/modext/widgets/element/modx.panel.tv.renders.js');
        $this->addJavascript($managerUrl.'assets/modext/widgets/resource/modx.grid.resource.security.local.js');
        $this->addJavascript($managerUrl.'assets/modext/widgets/resource/modx.panel.resource.tv.js');
        $this->addJavascript($managerUrl.'assets/modext/widgets/resource/modx.panel.resource.js');
        $this->addJavascript($managerUrl.'assets/modext/sections/resource/update.js');

        $this->addJavascript($containerxJsUrl.'containerx.js');
        $this->addLastJavascript($containerxJsUrl.'sections/category/update.js');
        $this->addLastJavascript($containerxJsUrl.'widgets/category/containerx.panel.category.js');
        $this->addLastJavascript($containerxJsUrl.'widgets/category/containerx.grid.resources.js');

        $this->loadExtendedFields();

        $this->addHtml('
        <script type="text/javascript">
        // <![CDATA[
        ContainerX.assetsUrl = "'.$containerxAssetsUrl.'";
        ContainerX.connectorUrl = "'.$connectorUrl.'";
        MODx.config.publish_document = "'.$this->canPublish.'";
        MODx.onDocFormRender = "'.$this->onDocFormRender.'";
        MODx.ctx = "'.$this->resource->get('context_key').'";
        Ext.onReady(function() {
            MODx.load({
                xtype: "containerx-page-category-update"
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

    public function loadExtendedFields() {
        /** @var ContainerXCategoryExtendedFields $extendedFields */
        $extendedFields = $this->resource->ExtendedFields;
        if($extendedFields){
            $extendedFieldsArray = $extendedFields->toArray();
            unset($extendedFieldsArray['category']);
            unset($extendedFieldsArray['id']);

            $this->resourceArray = array_merge($extendedFieldsArray, $this->resourceArray);
        }
    }
}