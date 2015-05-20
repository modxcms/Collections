Collections.panel.Template = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        border: false
        ,id: 'collections-panel-template'
        ,cls: 'container'
        ,url: Collections.config.connectorUrl
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
    Collections.panel.Template.superclass.constructor.call(this, config);
};

Ext.extend(Collections.panel.Template, MODx.FormPanel,{
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
            MODx.loadPage(MODx.action['collections:index'], 'action=template/update&id='+ o.result.object.id);
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
                            xtype: 'textfield'
                            ,fieldLabel: _('collections.template.sort_field')
                            ,name: 'sort_field'
                            ,allowBlank: false
                            ,value: (config.record) ? config.record.sort_field : 'id'
                        }]
                    },{
                        columnWidth:.4
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
                            ,fieldLabel: _('collections.template.bulk_actions')
                            ,name: 'bulk_actions'
                            ,hiddenName: 'bulk_actions'
                            ,value: (config.record) ? config.record.bulk_actions : false
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
                            ,fieldLabel: _('collections.template.allow_dd')
                            ,name: 'allow_dd'
                            ,hiddenName: 'allow_dd'
                            ,value: (config.record) ? config.record.allow_dd : true
                        }]
                    },{
                        columnWidth:.4
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
                        ,items: []
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
                            xtype: 'collections-combo-single-template'
                            ,fieldLabel: _('collections.template.child_template')
                            ,name: 'child_template'
                            ,hiddenName: 'child_template'
                            ,allowBlank: true
                            ,editable: true
                            ,addEmpty: true
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
            }]
        }];
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
                    }]
                }]
            }]
        }];
    }
});
Ext.reg('collections-panel-template',Collections.panel.Template);