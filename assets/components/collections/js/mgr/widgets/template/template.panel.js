collections.panel.Template = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        border: false
        ,id: 'collections-panel-template'
        ,cls: 'container'
        ,url: collections.config.connectorUrl
        ,baseParams: {
            action: 'mgr/template/create'
        }
        ,useLoadingMask: true
        ,items: this.getItems(config)
        ,listeners: {
            'setup': {
                fn: this.setup
                ,scope: this
            }
            ,'success': {
                fn: this.success
                ,scope: this
            }
        }
    });
    collections.panel.Template.superclass.constructor.call(this, config);
};

Ext.extend(collections.panel.Template, MODx.FormPanel,{
    setup: function() {
        if (this.config.isUpdate) {
            MODx.Ajax.request({
                url: this.config.url
                ,params: {
                    action: 'mgr/template/get'
                    ,id: MODx.request.id
                },
                listeners: {
                    'success': {
                        fn: function(r) {
                            this.getForm().setValues(r.object);

                            var fredDefaultBlueprint = this.find('name', 'fred_default_blueprint');
                            if (fredDefaultBlueprint[0]) {
                                fredDefaultBlueprint = fredDefaultBlueprint[0];
                                fredDefaultBlueprint.baseParams.template = r.object.child_template;
                            }

                            this.fireEvent('ready', r.object);
                            MODx.fireEvent('ready');
                        },
                        scope: this
                    }
                }
            });
        } else {
            this.fireEvent('ready');
            MODx.fireEvent('ready');
        }
    }


    ,success: function(o, r) {
        if (this.config.isUpdate == false) {
            MODx.loadPage('template/update', 'namespace=collections&id='+ o.result.object.id);
        }
    }

    ,getItems: function(config) {
        return [{
            html: '<h2>' + ((config.isUpdate == true)? _('collections.template.update_template') : _('collections.template.new_template')) + '</h2>'
            ,border: false
            ,cls: 'modx-page-header'
        },{
            name: 'id'
            ,xtype: 'hidden'
        },this.getGeneralFields(config),{
            html: '<br />'
            ,bodyCssClass: 'transparent-background'
        },this.getTemplateOptions(config),this.getColumnsGrid(config)];
    }

    ,getGeneralFields: function(config){
        return [{
            deferredRender: false
            ,border: true
            ,defaults: {
                autoHeight: true
                ,layout: 'form'
                ,labelWidth: 150
                ,bodyCssClass: 'main-wrapper'
                ,layoutOnTabChange: true
            }
            ,items: [{
                defaults: {
                    msgTarget: 'side'
                    ,autoHeight: true
                }
                ,cls: 'form-with-labels'
                ,border: false
                ,items: [{
                    layout: 'column'
                    ,border: false
                    ,height: 100
                    ,defaults: {
                        layout: 'form'
                        ,labelAlign: 'top'
                        ,labelSeparator: ''
                        ,anchor: '100%'
                        ,border: false
                    }
                    ,items: [{
                        columnWidth: 0.7
                        ,border: false
                        ,defaults: {
                            msgTarget: 'under'
                        }
                        ,items: [{
                            xtype: 'textfield'
                            ,fieldLabel: _('collections.template.name')
                            ,name: 'name'
                            ,anchor: '100%'
                            ,allowBlank: false
                        },{
                            xtype: 'textarea'
                            ,fieldLabel: _('collections.template.description')
                            ,name: 'description'
                            ,anchor: '100%'
                        }]
                    },{
                        columnWidth: 0.3
                        ,border: false
                        ,defaults: {
                            msgTarget: 'under'
                        }
                        ,items: [
                        // @TODO: Feature to have view for same resource template for selections and collections
                        //    {
                        //    xtype: 'collections-combo-view-for'
                        //    ,fieldLabel: _('collections.template.view_for')
                        //    ,name: 'view_for'
                        //    ,hiddenName: 'view_for'
                        //    ,anchor: '100%'
                        //},
                            {
                            xtype: 'modx-combo-boolean'
                            ,fieldLabel: _('collections.template.set_as_global')
                            ,name: 'global_template'
                            ,hiddenName: 'global_template'
                            ,anchor: '100%'
                            ,value: (config.record) ? config.record.global_template : false
                        },{
                            xtype: 'collections-combo-template'
                            ,id: 'collections-panel-template-field-templates'
                            ,fieldLabel: _('collections.template.default_for_templates')
                            ,name: 'templates'
                            ,hiddenName: 'templates[]'
                            ,anchor: '100%'
                        }]
                    }]
                }]
            }]
        }];
    }

    ,getColumnsGrid: function(config) {
        var items = [{
            html: '<br />'
            ,bodyCssClass: 'transparent-background'
        }];

        if (config.isUpdate == true) {
            items.push([{
                deferredRender: false
                ,border: true
                ,defaults: {
                    autoHeight: true
                    ,layout: 'form'
                    ,labelWidth: 150
                    ,bodyCssClass: 'main-wrapper'
                    ,layoutOnTabChange: true
                }
                ,items: [{
                    defaults: {
                        msgTarget: 'side'
                        ,autoHeight: true
                    }
                    ,cls: 'form-with-labels'
                    ,border: false
                    ,items: [{
                        layout: 'column'
                        ,border: false
                        ,height: 100
                        ,defaults: {
                            layout: 'form'
                            ,labelAlign: 'top'
                            ,labelSeparator: ''
                            ,anchor: '100%'
                            ,border: false
                        }
                        ,items: [{
                            columnWidth: 1
                            ,border: false
                            ,defaults: {
                                msgTarget: 'under'
                            }
                            ,items: [{
                                xtype: 'collections-grid-template-column'
                            }]
                        }]
                    }]
                }]
            }]);
        }

        return items;
    }

    ,getTemplateOptions: function(config) {
        return [{
                xtype: 'modx-vtabs'
                ,deferredRender: false
                ,items: [{
                    title: _('collections.template.general_settings')
                    ,items: this.getGeneralSettingsFields(config)
                },{
                    title: _('collections.template.collections_settings')
                    ,items: this.getCollectionsSettingsFields(config)
                },{
                    title: _('collections.template.selections_settings')
                    ,items: this.getSelectionsSettingsFields(config)
                }]
        }];

    }

    ,getGeneralSettingsFields: function(config) {
        return [{
            deferredRender: false
            ,border: false
            ,defaults: {
                autoHeight: true
                ,layout: 'form'
                ,labelWidth: 150
                ,bodyCssClass: 'main-wrapper'
                ,layoutOnTabChange: true
            }
            ,items: [{
                defaults: {
                    msgTarget: 'side'
                    ,autoHeight: true
                }
                ,cls: 'form-with-labels'
                ,border: false
                ,items: [{
                    layout: 'column'
                    ,border: false
                    ,height: 100
                    ,defaults: {
                        layout: 'form'
                        ,labelAlign: 'top'
                        ,labelSeparator: ''
                        ,anchor: '100%'
                        ,border: false
                    }
                    ,items: [{
                        columnWidth: 1
                        ,border: false
                        ,defaults: {
                            msgTarget: 'under'
                            ,anchor: '100%'
                        }
                        ,items: [{
                            xtype: 'textfield'
                            ,fieldLabel: _('collections.template.permanent_sort_before')
                            ,name: 'permanent_sort_before'
                        }]
                    }]
                }]
            },{
                defaults: {
                    msgTarget: 'side'
                    ,autoHeight: true
                }
                ,cls: 'form-with-labels'
                ,border: false
                ,items: [{
                    layout: 'column'
                    ,border: false
                    ,height: 100
                    ,defaults: {
                        layout: 'form'
                        ,labelAlign: 'top'
                        ,labelSeparator: ''
                        ,anchor: '100%'
                        ,border: false
                    }
                    ,items: [{
                        columnWidth:.3
                        ,border: false
                        ,defaults: {
                            msgTarget: 'under'
                            ,anchor: '100%'
                        }
                        ,items: [{
                            xtype: 'textfield'
                            ,fieldLabel: _('collections.template.sort_field')
                            ,name: 'sort_field'
                            ,allowBlank: false
                            ,value: (config.record) ? config.record.sort_field : 'id'
                        }]
                    },{
                        columnWidth:.3
                        ,border: false
                        ,defaults: {
                            msgTarget: 'under'
                            ,anchor: '100%'
                        }
                        ,items: [{
                            xtype: 'collections-combo-sort-dir'
                            ,fieldLabel: _('collections.template.sort_dir')
                            ,name: 'sort_dir'
                            ,hiddenName: 'sort_dir'
                            ,allowBlank: false
                            ,value: (config.record) ? config.record.sort_dir : 'asc'
                        }]
                    },{
                        columnWidth:.4
                        ,border: false
                        ,defaults: {
                            msgTarget: 'under'
                            ,anchor: '100%'
                        }
                        ,items: [{
                            xtype: 'collections-combo-sort-type'
                            ,fieldLabel: _('collections.template.sort_type')
                            ,name: 'sort_type'
                            ,hiddenName: 'sort_type'
                            ,allowBlank: false
                            ,value: (config.record) ? config.record.sort_type : null
                        }]
                    }]
                }]
            },{
                defaults: {
                    msgTarget: 'side'
                    ,autoHeight: true
                }
                ,cls: 'form-with-labels'
                ,border: false
                ,items: [{
                    layout: 'column'
                    ,border: false
                    ,height: 100
                    ,defaults: {
                        layout: 'form'
                        ,labelAlign: 'top'
                        ,labelSeparator: ''
                        ,anchor: '100%'
                        ,border: false
                    }
                    ,items: [{
                        columnWidth: 1
                        ,border: false
                        ,defaults: {
                            msgTarget: 'under'
                            ,anchor: '100%'
                        }
                        ,items: [{
                            xtype: 'textfield'
                            ,fieldLabel: _('collections.template.permanent_sort_after')
                            ,name: 'permanent_sort_after'
                        }]
                    }]
                }]
            },{
                defaults: {
                    msgTarget: 'side'
                    ,autoHeight: true
                }
                ,cls: 'form-with-labels'
                ,border: false
                ,items: [{
                    layout: 'column'
                    ,border: false
                    ,height: 100
                    ,defaults: {
                        layout: 'form'
                        ,labelAlign: 'top'
                        ,labelSeparator: ''
                        ,anchor: '100%'
                        ,border: false
                    }
                    ,items: [{
                        columnWidth:.3
                        ,border: false
                        ,defaults: {
                            msgTarget: 'under'
                            ,anchor: '100%'
                        }
                        ,items: [{
                            xtype: 'numberfield'
                            ,allowDecimals: false
                            ,allowNegative: false
                            ,fieldLabel: _('collections.template.page_size')
                            ,name: 'page_size'
                            ,allowBlank: false
                            ,value: (config.record) ? config.record.page_size : 20
                        }]
                    },{
                        columnWidth:.3
                        ,border: false
                        ,defaults: {
                            msgTarget: 'under'
                            ,anchor: '100%'
                        }
                        ,items: [{
                            xtype: 'modx-combo-boolean'
                            ,fieldLabel: _('collections.template.bulk_actions')
                            ,name: 'bulk_actions'
                            ,hiddenName: 'bulk_actions'
                            ,value: (config.record) ? config.record.bulk_actions : false
                        }]
                    },{
                        columnWidth:.4
                        ,border: false
                        ,defaults: {
                            msgTarget: 'under'
                            ,anchor: '100%'
                        }
                        ,items: [{
                            xtype: 'modx-combo-boolean'
                            ,fieldLabel: _('collections.template.allow_dd')
                            ,name: 'allow_dd'
                            ,hiddenName: 'allow_dd'
                            ,value: (config.record) ? config.record.allow_dd : true
                        }]
                    }]
                }]
            },{
                defaults: {
                    msgTarget: 'side'
                    ,autoHeight: true
                }
                ,cls: 'form-with-labels'
                ,border: false
                ,items: [{
                    layout: 'column'
                    ,border: false
                    ,height: 100
                    ,defaults: {
                        layout: 'form'
                        ,labelAlign: 'top'
                        ,labelSeparator: ''
                        ,anchor: '100%'
                        ,border: false
                    }
                    ,items: [{
                        columnWidth:.3
                        ,border: false
                        ,defaults: {
                            msgTarget: 'under'
                            ,anchor: '100%'
                        }
                        ,items: [{
                            xtype: 'textfield'
                            ,fieldLabel: _('collections.template.tab_label')
                            ,name: 'tab_label'
                            ,value: (config.record) ? config.record.tab_label : 'collections.children'
                        }]
                    },{
                        columnWidth:.3
                        ,border: false
                        ,defaults: {
                            msgTarget: 'under'
                            ,anchor: '100%'
                        }
                        ,items: [{
                            xtype: 'collections-combo-content-place'
                            ,fieldLabel: _('collections.template.content_place')
                            ,name: 'content_place'
                            ,hiddenName: 'content_place'
                            ,value: (config.record) ? config.record.button_label : 'original'
                        }]
                    },{
                        columnWidth:.4
                        ,border: false
                        ,defaults: {
                            msgTarget: 'under'
                            ,anchor: '100%'
                        }
                        ,items: []
                    }]
                }]
            },{
                defaults: {
                    msgTarget: 'side'
                    ,autoHeight: true
                }
                ,cls: 'form-with-labels'
                ,border: false
                ,items: [{
                    layout: 'column'
                    ,border: false
                    ,height: 100
                    ,defaults: {
                        layout: 'form'
                        ,labelAlign: 'top'
                        ,labelSeparator: ''
                        ,anchor: '100%'
                        ,border: false
                    }
                    ,items: [{
                        columnWidth:.3
                        ,border: false
                        ,defaults: {
                            msgTarget: 'under'
                            ,anchor: '100%'
                        }
                        ,items: [{
                            xtype: 'modx-combo-boolean'
                            ,fieldLabel: _('collections.template.search_query_title_only')
                            ,name: 'search_query_title_only'
                            ,hiddenName: 'search_query_title_only'
                            ,value: (config.record) ? config.record.search_query_title_only : false
                        }]
                    },{
                        columnWidth:.3
                        ,border: false
                        ,defaults: {
                            msgTarget: 'under'
                            ,anchor: '100%'
                        }
                        ,items: [{
                            xtype: 'modx-combo-boolean'
                            ,fieldLabel: _('collections.template.search_query_exclude_tvs')
                            ,name: 'search_query_exclude_tvs'
                            ,hiddenName: 'search_query_exclude_tvs'
                            ,value: (config.record) ? config.record.search_query_exclude_tvs : false
                        }]
                    },{
                        columnWidth:.4
                        ,border: false
                        ,defaults: {
                            msgTarget: 'under'
                            ,anchor: '100%'
                        }
                        ,items: [{
                            xtype: 'modx-combo-boolean'
                            ,fieldLabel: _('collections.template.search_query_exclude_tagger')
                            ,name: 'search_query_exclude_tagger'
                            ,hiddenName: 'search_query_exclude_tagger'
                            ,value: (config.record) ? config.record.search_query_exclude_tagger : false
                        }]
                    }]
                }]
            },{
                defaults: {
                    msgTarget: 'side'
                    ,autoHeight: true
                }
                ,cls: 'form-with-labels'
                ,border: false
                ,items: [{
                    layout: 'column'
                    ,border: false
                    ,height: 100
                    ,defaults: {
                        layout: 'form'
                        ,labelAlign: 'top'
                        ,labelSeparator: ''
                        ,anchor: '100%'
                        ,border: false
                    }
                    ,items: [{
                        columnWidth: 1
                        ,border: false
                        ,defaults: {
                            msgTarget: 'under'
                            ,anchor: '100%'
                        }
                        ,items: [{
                            xtype: 'textfield'
                            ,fieldLabel: _('collections.template.context_menu')
                            ,description: _('collections.template.context_menu_desc')
                            ,name: 'context_menu'
                            ,value: (config.record) ? config.record.context_menu : 'view,edit,duplicate,publish,unpublish,-,delete,undelete,remove,-,unlink'
                        }]
                    }]
                }]
            },{
                defaults: {
                    msgTarget: 'side'
                    ,autoHeight: true
                }
                ,cls: 'form-with-labels'
                ,border: false
                ,items: [{
                    layout: 'column'
                    ,border: false
                    ,height: 100
                    ,defaults: {
                        layout: 'form'
                        ,labelAlign: 'top'
                        ,labelSeparator: ''
                        ,anchor: '100%'
                        ,border: false
                    }
                    ,items: [{
                        columnWidth: 1
                        ,border: false
                        ,defaults: {
                            msgTarget: 'under'
                            ,anchor: '100%'
                        }
                        ,items: [{
                            xtype: 'textfield'
                            ,fieldLabel: _('collections.template.buttons')
                            ,description: _('collections.template.buttons_desc')
                            ,name: 'buttons'
                            ,value: (config.record) ? config.record.buttons : 'view,edit,duplicate,publish:orange,unpublish,delete,undelete,remove,unlink'
                        }]
                    }]
                }]
            }]
        }];
    }

    ,getCollectionsSettingsFields: function(config) {
        var items = [
            {
            defaults: {
                msgTarget: 'side'
                ,autoHeight: true
            }
            ,cls: 'form-with-labels'
            ,border: false
            ,items: [{
                layout: 'column'
                ,border: false
                ,height: 100
                ,defaults: {
                    layout: 'form'
                    ,labelAlign: 'top'
                    ,labelSeparator: ''
                    ,anchor: '100%'
                    ,border: false
                }
                ,items: [{
                    columnWidth:.5
                    ,border: false
                    ,defaults: {
                        msgTarget: 'under'
                        ,anchor: '100%'
                    }
                    ,items: [{
                        xtype: 'modx-combo-boolean'
                        ,fieldLabel: _('collections.template.resource_type_selection')
                        ,name: 'resource_type_selection'
                        ,hiddenName: 'resource_type_selection'
                        ,value: (config.record) ? config.record.resource_type_selection : true
                    }]
                },{
                    columnWidth:.5
                    ,border: false
                    ,defaults: {
                        msgTarget: 'under'
                        ,anchor: '100%'
                    }
                    ,items: [{
                        xtype: 'textfield'
                        ,fieldLabel: _('collections.template.button_label')
                        ,name: 'button_label'
                        ,value: (config.record) ? config.record.button_label : 'collections.children.create'
                    }]
                }]
            }]
        },{
            defaults: {
                msgTarget: 'side'
                ,autoHeight: true
            }
            ,cls: 'form-with-labels'
            ,border: false
            ,items: [{
                layout: 'column'
                ,border: false
                ,height: 100
                ,defaults: {
                    layout: 'form'
                    ,labelAlign: 'top'
                    ,labelSeparator: ''
                    ,anchor: '100%'
                    ,border: false
                }
                ,items: [{
                    columnWidth:.5
                    ,border: false
                    ,defaults: {
                        msgTarget: 'under'
                        ,anchor: '100%'
                    }
                    ,items: [{
                        xtype: 'modx-combo-boolean'
                        ,fieldLabel: _('collections.template.show_quick_create')
                        ,name: 'show_quick_create'
                        ,hiddenName: 'show_quick_create'
                        ,value: (config.record) ? config.record.show_quick_create : true
                    }]
                },{
                    columnWidth:.5
                    ,border: false
                    ,defaults: {
                        msgTarget: 'under'
                        ,anchor: '100%'
                    }
                    ,items: [{
                        xtype: 'textfield'
                        ,fieldLabel: _('collections.template.quick_create_label')
                        ,name: 'quick_create_label'
                        ,value: (config.record) ? config.record.quick_create_label : 'collections.children.quick_create'
                    }]
                }]
            }]
        },{
            defaults: {
                msgTarget: 'side'
                ,autoHeight: true
            }
            ,cls: 'form-with-labels'
            ,border: false
            ,items: [{
                layout: 'column'
                ,border: false
                ,height: 100
                ,defaults: {
                    layout: 'form'
                    ,labelAlign: 'top'
                    ,labelSeparator: ''
                    ,anchor: '100%'
                    ,border: false
                }
                ,items: [{
                    columnWidth:.5
                    ,border: false
                    ,defaults: {
                        msgTarget: 'under'
                        ,anchor: '100%'
                    }
                    ,items: [{
                        xtype: 'collections-combo-single-template'
                        ,fieldLabel: _('collections.template.child_template')
                        ,name: 'child_template'
                        ,hiddenName: 'child_template'
                        ,allowBlank: true
                        ,editable: true
                        ,addEmpty: true
                        ,listeners: {
                            select: function (combo, record) {
                                var fredDefaultBlueprint = this.find('name', 'fred_default_blueprint');
                                if (!fredDefaultBlueprint[0]) return;

                                fredDefaultBlueprint = fredDefaultBlueprint[0];
                                fredDefaultBlueprint.useTemplate(record.id);
                            },
                            scope: this
                        }
                    }]
                },{
                    columnWidth:.5
                    ,border: false
                    ,defaults: {
                        msgTarget: 'under'
                        ,anchor: '100%'
                    }
                    ,items: [{
                        xtype: 'modx-combo-class-derivatives'
                        ,fieldLabel: _('collections.template.child_resource_type')
                        ,name: 'child_resource_type'
                        ,hiddenName: 'child_resource_type'
                        ,allowBlank: false
                        ,editable: false
                        ,value: (config.record) ? config.record.child_resource_type : 'modDocument'
                    }]
                }]
            }]
        },{
            defaults: {
                msgTarget: 'side'
                ,autoHeight: true
            }
            ,cls: 'form-with-labels'
            ,border: false
            ,items: [{
                layout: 'column'
                ,border: false
                ,height: 100
                ,defaults: {
                    layout: 'form'
                    ,labelAlign: 'top'
                    ,labelSeparator: ''
                    ,anchor: '100%'
                    ,border: false
                }
                ,items: [{
                    columnWidth:.5
                    ,border: false
                    ,defaults: {
                        msgTarget: 'under'
                        ,anchor: '100%'
                    }
                    ,items: [{
                        xtype: 'collections-combo-extended-boolean'
                        ,fieldLabel: _('collections.template.child_published')
                        ,name: 'child_published'
                        ,hiddenName: 'child_published'
                        ,value: (config.record) ? config.record.child_published : null
                    }]
                },{
                    columnWidth:.5
                    ,border: false
                    ,defaults: {
                        msgTarget: 'under'
                        ,anchor: '100%'
                    }
                    ,items: [{
                        xtype: 'collections-combo-content-type'
                        ,fieldLabel: _('collections.template.child_content_type')
                        ,name: 'child_content_type'
                        ,hiddenName: 'child_content_type'
                        ,allowBlank: true
                        ,editable: false
                        ,value: (config.record) ? config.record.child_content_type : 0
                    }]
                }]
            }]
        },{
            defaults: {
                msgTarget: 'side'
                ,autoHeight: true
            }
            ,cls: 'form-with-labels'
            ,border: false
            ,items: [{
                layout: 'column'
                ,border: false
                ,height: 100
                ,defaults: {
                    layout: 'form'
                    ,labelAlign: 'top'
                    ,labelSeparator: ''
                    ,anchor: '100%'
                    ,border: false
                }
                ,items: [{
                    columnWidth:.5
                    ,border: false
                    ,defaults: {
                        msgTarget: 'under'
                        ,anchor: '100%'
                    }
                    ,items: [{
                        xtype: 'collections-combo-extended-boolean'
                        ,fieldLabel: _('collections.template.child_hide_from_menu')
                        ,name: 'child_hide_from_menu'
                        ,hiddenName: 'child_hide_from_menu'
                        ,value: (config.record) ? config.record.child_hide_from_menu : null
                    }]
                },{
                    columnWidth:.5
                    ,border: false
                    ,defaults: {
                        msgTarget: 'under'
                        ,anchor: '100%'
                    }
                    ,items: [{
                        xtype: 'collections-combo-extended-boolean'
                        ,fieldLabel: _('collections.template.child_cacheable')
                        ,name: 'child_cacheable'
                        ,hiddenName: 'child_cacheable'
                        ,value: (config.record) ? config.record.child_cacheable : null
                    }]
                }]
            }]
        },{
            defaults: {
                msgTarget: 'side'
                ,autoHeight: true
            }
            ,cls: 'form-with-labels'
            ,border: false
            ,items: [{
                layout: 'column'
                ,border: false
                ,height: 100
                ,defaults: {
                    layout: 'form'
                    ,labelAlign: 'top'
                    ,labelSeparator: ''
                    ,anchor: '100%'
                    ,border: false
                }
                ,items: [{
                    columnWidth:.5
                    ,border: false
                    ,defaults: {
                        msgTarget: 'under'
                        ,anchor: '100%'
                    }
                    ,items: [{
                        xtype: 'collections-combo-extended-boolean'
                        ,fieldLabel: _('collections.template.child_richtext')
                        ,name: 'child_richtext'
                        ,hiddenName: 'child_richtext'
                        ,value: (config.record) ? config.record.child_richtext : null
                    }]
                },{
                    columnWidth:.5
                    ,border: false
                    ,defaults: {
                        msgTarget: 'under'
                        ,anchor: '100%'
                    }
                    ,items: [{
                        xtype: 'collections-combo-extended-boolean'
                        ,fieldLabel: _('collections.template.child_searchable')
                        ,name: 'child_searchable'
                        ,hiddenName: 'child_searchable'
                        ,value: (config.record) ? config.record.child_searchable : null
                    }]
                }]
            }]
        },{
            defaults: {
                msgTarget: 'side'
                ,autoHeight: true
            }
            ,cls: 'form-with-labels'
            ,border: false
            ,items: [{
                layout: 'column'
                ,border: false
                ,height: 100
                ,defaults: {
                    layout: 'form'
                    ,labelAlign: 'top'
                    ,labelSeparator: ''
                    ,anchor: '100%'
                    ,border: false
                }
                ,items: [{
                    columnWidth:.5
                    ,border: false
                    ,defaults: {
                        msgTarget: 'under'
                        ,anchor: '100%'
                    }
                    ,items: [{
                        xtype: 'collections-combo-content-disposition-extended'
                        ,fieldLabel: _('collections.template.child_content_disposition')
                        ,name: 'child_content_disposition'
                        ,hiddenName: 'child_content_disposition'
                        ,value: (config.record) ? config.record.child_content_disposition : null
                    }]
                },{
                    columnWidth:.5
                    ,border: false
                    ,defaults: {
                        msgTarget: 'under'
                        ,anchor: '100%'
                    }
                    ,items: []
                }]
            }]
        },{
            defaults: {
                msgTarget: 'side'
                ,autoHeight: true
            }
            ,cls: 'form-with-labels'
            ,border: false
            ,items: [{
                layout: 'column'
                ,border: false
                ,height: 100
                ,defaults: {
                    layout: 'form'
                    ,labelAlign: 'top'
                    ,labelSeparator: ''
                    ,anchor: '100%'
                    ,border: false
                }
                ,items: [{
                    columnWidth:.5
                    ,border: false
                    ,defaults: {
                        msgTarget: 'under'
                        ,anchor: '100%'
                    }
                    ,items: [{
                        xtype: 'textfield'
                        ,fieldLabel: _('collections.template.back_to_collection')
                        ,name: 'back_to_collection_label'
                        ,value: (config.record) ? config.record.back_to_collection_label : 'collections.children.back_to_collection_label'
                    }]
                },{
                    columnWidth:.5
                    ,border: false
                    ,defaults: {
                        msgTarget: 'under'
                        ,anchor: '100%'
                    }
                    ,items: [{
                        xtype: 'textfield'
                        ,fieldLabel: _('collections.template.parent')
                        ,name: 'parent'
                        ,value: (config.record) ? config.record.parent : ''
                    }]
                }]
            }]
        },{
            defaults: {
                msgTarget: 'side'
                ,autoHeight: true
            }
            ,cls: 'form-with-labels'
            ,border: false
            ,items: [{
                layout: 'column'
                ,border: false
                ,height: 100
                ,defaults: {
                    layout: 'form'
                    ,labelAlign: 'top'
                    ,labelSeparator: ''
                    ,anchor: '100%'
                    ,border: false
                }
                ,items: [{
                    columnWidth: 1
                    ,border: false
                    ,defaults: {
                        msgTarget: 'under'
                        ,anchor: '100%'
                    }
                    ,items: [{
                        xtype: 'textfield'
                        ,fieldLabel: _('collections.template.allowed_resource_types')
                        ,description: _('collections.template.allowed_resource_types_desc')
                        ,name: 'allowed_resource_types'
                        ,value: (config.record) ? config.record.allowed_resource_types : ''
                    }]
                }]
            }]
        }
        ];

        if (config.fredInstalled) {
            items.push({
                defaults: {
                    msgTarget: 'side',
                    autoHeight: true
                },
                cls: 'form-with-labels',
                border: false,
                items: [
                    {
                        layout: 'column',
                        border: false,
                        height: 100,
                        defaults: {
                            layout: 'form',
                            labelAlign: 'top',
                            labelSeparator: '',
                            anchor: '100%',
                            border: false
                        },
                        items: [
                            {
                                columnWidth: 1,
                                border: false,
                                defaults: {
                                    msgTarget: 'under',
                                    anchor: '100%'
                                },
                                items: [
                                    {
                                        xtype: 'collections-combo-fred-blueprints',
                                        fieldLabel: _('collections.template.fred_default_blueprint'),
                                        description: _('collections.template.fred_default_blueprint_desc'),
                                        name: 'fred_default_blueprint',
                                        hiddenName: 'fred_default_blueprint',
                                        addNone: 1,
                                        useTemplate: function(template) {
                                            this.baseParams.template = template;
                                            this.store.on('load', function(store, records, options) {
                                                if (records.length === 2) {
                                                    this.setValue(options.params.uuid);
                                                } else {
                                                    this.setValue("");
                                                }

                                                this.lastQuery = null;
                                            }, this, {single: true});
                                            this.store.load({params: {uuid: this.getValue()}});
                                        }
                                    }
                                ]
                            }
                        ]
                    }
                ]
            });
        }

        return [
            {
                deferredRender: false,
                border: false,
                defaults: {
                    autoHeight: true,
                    layout: 'form',
                    labelWidth: 150,
                    bodyCssClass: 'main-wrapper',
                    layoutOnTabChange: true
                },
                items: items
            }
        ];
    }

    ,getSelectionsSettingsFields: function(config) {
        return [{
            deferredRender: false
            ,border: false
            ,defaults: {
                autoHeight: true
                ,layout: 'form'
                ,labelWidth: 150
                ,bodyCssClass: 'main-wrapper'
                ,layoutOnTabChange: true
            }
            ,items: [{
                defaults: {
                    msgTarget: 'side'
                    ,autoHeight: true
                }
                ,cls: 'form-with-labels'
                ,border: false
                ,items: [{
                    layout: 'column'
                    ,border: false
                    ,height: 100
                    ,defaults: {
                        layout: 'form'
                        ,labelAlign: 'top'
                        ,labelSeparator: ''
                        ,anchor: '100%'
                        ,border: false
                    }
                    ,items: [{
                        columnWidth:.5
                        ,border: false
                        ,defaults: {
                            msgTarget: 'under'
                            ,anchor: '100%'
                        }
                        ,items: [{
                            xtype: 'textfield'
                            ,fieldLabel: _('collections.template.link_label')
                            ,name: 'link_label'
                            ,value: (config.record) ? config.record.link_label : 'selections.create'
                        }]
                    },{
                        columnWidth:.5
                        ,border: false
                        ,defaults: {
                            msgTarget: 'under'
                            ,anchor: '100%'
                        }
                        ,items: [{
                            xtype: 'textfield'
                            ,fieldLabel: _('collections.template.back_to_selection')
                            ,name: 'back_to_selection_label'
                            ,value: (config.record) ? config.record.back_to_selection_label : 'selections.back_to_selection_label'
                        }]
                    }]
                }]
            },{
                defaults: {
                    msgTarget: 'side'
                    ,autoHeight: true
                }
                ,cls: 'form-with-labels'
                ,border: false
                ,items: [{
                    layout: 'column'
                    ,border: false
                    ,height: 100
                    ,defaults: {
                        layout: 'form'
                        ,labelAlign: 'top'
                        ,labelSeparator: ''
                        ,anchor: '100%'
                        ,border: false
                    }
                    ,items: [{
                        columnWidth:.5
                        ,border: false
                        ,defaults: {
                            msgTarget: 'under'
                            ,anchor: '100%'
                        }
                        ,items: [{
                            xtype: 'textfield'
                            ,fieldLabel: _('collections.template.selection_create_sort')
                            ,name: 'selection_create_sort'
                            ,value: (config.record) ? config.record.selection_create_sort : 'id:desc'
                        }]
                    },{
                        columnWidth:.5
                        ,border: false
                        ,defaults: {
                            msgTarget: 'under'
                            ,anchor: '100%'
                        }
                        ,items: [{
                            xtype: 'textfield'
                            ,fieldLabel: _('collections.template.selection_link_condition')
                            ,description: _('collections.template.selection_link_condition_desc')
                            ,name: 'selection_link_condition'
                            ,value: (config.record) ? config.record.selection_link_condition : ''
                        }]
                    }]
                }]
            }]
        }];
    }
});
Ext.reg('collections-panel-template',collections.panel.Template);
