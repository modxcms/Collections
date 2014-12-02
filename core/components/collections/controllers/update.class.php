<?php
/**
 * Update controller for Collections Container
 *
 * @package collections
 * @subpackage controller
 */
class CollectionContainerUpdateManagerController extends ResourceUpdateManagerController {
    /** @var modResource $resource */
    public $resource;

    public function getLanguageTopics() {
        return array('resource','collections:default', 'collections:templates', 'collections:custom', 'collections:selections');
    }

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
        $this->addLastJavascript($collectionsJsUrl.'widgets/category/collections.grid.resources.js');
        $this->addLastJavascript($collectionsJsUrl.'widgets/category/collections.window.js');
        $this->addLastJavascript($collectionsJsUrl.'extra/collections.combo.js');
        $this->addLastJavascript($collectionsJsUrl.'extra/griddraganddrop.js');
        $this->addLastJavascript($collectionsJsUrl.'extra/collections.renderers.js');

        $userJS = $this->modx->getOption('collections.user_js', '');
        if ($userJS != '') {
            $this->addLastJavascript($userJS);
        }

        $collectionsTemplate = $this->getCollectionsTemplate();

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
        Ext.onReady(function() {
            MODx.load({
                xtype: "collections-page-category-update"
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

    public function getCollectionsTemplate() {
        /** @var CollectionTemplate $template */
        $template = $this->modx->collections->getCollectionsView($this->resource);

        $c = $this->modx->newQuery('CollectionTemplateColumn');
        $c->sortby('position', 'ASC');
        $c->where(array(
            'template' => $template->id
        ));

        /** @var CollectionTemplateColumn[] $columns */
        $columns = $this->modx->getIterator('CollectionTemplateColumn', $c);

        $derivates = array();

        if ($template->resource_type_selection) {
            $response = $this->modx->runProcessor('mgr/extra/getderivates', array(
                'skip' => 'modXMLRPCResource',
                'class' => 'modResource',
            ), array(
                'processors_path' => $this->modx->collections->getOption('processorsPath'),
            ));

            $response = $this->modx->fromJSON($response->response);

            if ($response != '') {
                if ($template->allowed_resource_types == '') {
                    foreach ($response['results'] as $type) {
                        $derivates[] = $type;
                    }
                } else {
                    $allowedTypes = $this->modx->collections->explodeAndClean($template->allowed_resource_types);

                    foreach ($allowedTypes as $type) {
                        if (isset($response['results'][$type])) {
                            $derivates[] = $response['results'][$type];
                        }
                    }
                }

            }
        }

        $templateOptions = array(
            'fields' => array('actions', 'action_edit', 'preview_url', 'menu_actions'),
            'columns' => array(),
            'sort' => array(
                'field' => $template->sort_field,
                'dir' => $template->sort_dir,
            ),
            'pageSize' => $template->page_size,
            'bulkActions' => $template->bulk_actions,
            'allowDD' => $template->allow_dd,
            'resource_type_selection' => $template->resource_type_selection,
            'children' => array(
                'template' => $template->child_template,
                'resource_type' => $template->child_resource_type,
            ),
            'tab_label' => $template->tab_label,
            'button_label' => $template->button_label,
            'link_label' => $template->link_label,
            'content_place' => $template->content_place,
            'context_menu' => $this->modx->collections->explodeAndClean($template->context_menu, ',', 1),
            'resourceDerivatives' => $derivates,
            'selection_create_sort' => $template->selection_create_sort,
        );

        foreach ($columns as $column) {
            $templateOptions['fields'][] = $column->name;

            $header = $this->modx->lexicon($column->label);
            if ($header == null) {
                $header = $column->label;
            }

            $columnDef = array(
                'header' => $header,
                'dataIndex' => $column->name,
                'hidden' => $column->hidden,
                'sortable' => $column->sortable,
                'width' => $column->width,
            );

            if ($column->editor != '') {
                $editorObj = $this->modx->fromJSON($column->editor);
                if ($editorObj == null) {
                    $editorObj = array(
                        'xtype' => $column->editor,
                        'renderer' => false
                    );
                }

                $columnDef['editor'] = $editorObj;
            }

            if ($column->renderer != '') {
                $columnDef['renderer'] = $column->renderer;
            }

            $templateOptions['columns'][] = $columnDef;
        }

        return $this->modx->toJSON($templateOptions);
    }

    public function loadConfig() {
        $config = $this->modx->getObject('CollectionSetting', array('collection' => $this->resource->id));
        if ($config) {
            $this->resourceArray['collections_template'] = $config->template;
        }
    }
}