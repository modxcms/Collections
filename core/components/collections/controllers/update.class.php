<?php

use Collections\Model\CollectionSetting;
use Collections\Model\CollectionTemplateColumn;
use Collections\Utils;
use MODX\Revolution\modResource;

/**
 * Update controller for Collections Container
 *
 * @package collections
 * @subpackage controller
 */
class CollectionContainerUpdateManagerController extends ResourceUpdateManagerController {
    /** @var modResource $resource */
    public $resource;

    /** @var Collections\Collections $collections */
    public $collections;

    public function initialize()
    {
        $this->collections = $this->modx->services->get('collections');

        parent::initialize();
    }

    public function getLanguageTopics() {
        return ['resource','collections:default', 'collections:templates', 'collections:custom', 'collections:selections'];
    }

    /**
     * Register custom CSS/JS for the page
     * @return void
     */
    public function loadCustomCssJs() {
        $managerUrl = $this->context->getOption('manager_url', MODX_MANAGER_URL, $this->modx->_userConfig);
        $collectionsAssetsUrl = $this->modx->getOption('collections.assets_url',null,$this->modx->getOption('assets_url',null,MODX_ASSETS_URL).'components/collections/');
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
        collections.assetsUrl = "'.$collectionsAssetsUrl.'";
        collections.config = '.json_encode($this->collections->config).';
        MODx.config.publish_document = "'.$this->canPublish.'";
        MODx.onDocFormRender = "'.$this->onDocFormRender.'";
        MODx.ctx = "'.$this->resource->get('context_key').'";
        collections.template = ' . $collectionsTemplate . ';
        Ext.onReady(function() {
            MODx.load({
                xtype: "collections-page-category-update"
                ,resource: "'.$this->resource->get('id').'"
                ,record: '.json_encode($this->resourceArray).'
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
        $template = $this->collections->getCollectionsView($this->resource);

        $c = $this->modx->newQuery(CollectionTemplateColumn::class);
        $c->sortby('position', 'ASC');
        $c->where([
            'template' => $template->id
        ]);

        /** @var CollectionTemplateColumn[] $columns */
        $columns = $this->modx->getIterator(CollectionTemplateColumn::class, $c);

        $derivates = [];

        if ($template->resource_type_selection) {
            $response = $this->modx->runProcessor('Collections\\Processors\\Extra\\GetDerivates', [
                'skip' => 'modXMLRPCResource',
                'class' => 'MODX\\Revolution\\modResource',
            ]);

            $response = json_decode($response->response, true);

            if ($response != '') {
                if ($template->allowed_resource_types == '') {
                    foreach ($response['results'] as $type) {
                        $derivates[] = $type;
                    }
                } else {
                    $allowedTypes = Utils::explodeAndClean($template->allowed_resource_types);

                    foreach ($allowedTypes as $type) {
                        if (isset($response['results'][$type])) {
                            $derivates[] = $response['results'][$type];
                        }
                    }
                }

            }
        }

        $parent = !empty($template->parent) ? $template->parent : '';
        if (substr($parent,0,8) == '@SNIPPET'){
            $snippet = trim(substr($parent,8));
            $properties = [
                'resource' => & $this->resource
            ];
            $parent = $this->modx->runSnippet($snippet,$properties);
        }
        $parent_context = $this->resource->get('context_key');
        if (!empty($parent)){
            //we have a custom parent - try to get the context_key
            if ($p_resource = $this->modx->getObject(modResource::class,$parent)){
                $parent_context = $p_resource->get('context_key');
            }
        }else{
            $parent = $this->resource->get('id');
        }

        $templateOptions = [
            'fields' => ['actions', 'action_edit', 'preview_url', 'menu_actions', 'icons', 'has_children'],
            'columns' => [],
            'sort' => [
                'field' => $template->sort_field . ':' . $template->sort_type,
                'dir' => $template->sort_dir,
            ],
            'pageSize' => $template->page_size,
            'bulkActions' => $template->bulk_actions,
            'allowDD' => $template->allow_dd,
            'resource_type_selection' => $template->resource_type_selection,
            'show_quick_create' => $template->show_quick_create,
            'quick_create_label' => $template->quick_create_label,
            'children' => [
                'template' => $template->child_template,
                'resource_type' => $template->child_resource_type,
                'rich_text' => $template->child_richtext,
                'cacheable' => $template->child_cacheable,
                'content_type' => $template->child_content_type,
                'published' => $template->child_published,
                'content_disposition' => $template->child_content_disposition,
                'hide_from_menu' => $template->child_hide_from_menu,
                'searchable' => $template->child_searchable
            ],
            'tab_label' => $template->tab_label,
            'button_label' => $template->button_label,
            'link_label' => $template->link_label,
            'content_place' => $template->content_place,
            'context_menu' => Utils::explodeAndClean($template->context_menu, ',', 1),
            'resourceDerivatives' => $derivates,
            'selection_create_sort' => $template->selection_create_sort,
            'parent' => $parent,
            'parent_context' => $parent_context,
            'permanent_sort' => [
                'before' => $template->permanent_sort_before,
                'after' => $template->permanent_sort_after,
            ],
            'search_query_exclude_tvs' => $template->search_query_exclude_tvs,
            'search_query_exclude_tagger' => $template->search_query_exclude_tagger,
            'search_query_title_only' => $template->search_query_title_only,
        ];

        foreach ($columns as $column) {
            $templateOptions['fields'][] = $column->name;

            $header = $this->modx->lexicon($column->label);
            if ($header == null) {
                $header = $column->label;
            }

            $columnDef = [
                'header' => $header,
                'dataIndex' => $column->name,
                'hidden' => $column->hidden,
                'sortable' => $column->sortable,
                'width' => $column->width,
            ];

            if ($column->editor != '') {
                $editorObj = json_decode($column->editor, true);
                if ($editorObj == null) {
                    $editorObj = [
                        'xtype' => $column->editor,
                        'renderer' => false
                    ];
                }

                $columnDef['editor'] = $editorObj;
            }

            if ($column->renderer != '') {
                $columnDef['renderer'] = $column->renderer;
            }

            $templateOptions['columns'][] = $columnDef;
        }

        return json_encode($templateOptions);
    }

    public function loadConfig() {
        $config = $this->modx->getObject(CollectionSetting::class, ['collection' => $this->resource->id]);
        if ($config) {
            $this->resourceArray['collections_template'] = $config->template;
        }
    }
}
