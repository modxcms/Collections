collections.window.Selection = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        title: _('collections.selection.create')
        ,closeAction: 'close'
        ,isUpdate: false
        ,url: collections.config.connectorUrl
        ,action: 'mgr/selection/create'
        ,fields: this.getFields(config)
    });
    collections.window.Selection.superclass.constructor.call(this,config);

    this.on('show',function() {
        var fld = this.fp.getForm().items.itemAt(0);
        fld.focus(false,200);
    },this);
};
Ext.extend(collections.window.Selection,MODx.Window, {

    getFields: function(config) {
        return [{
            xtype: 'collections-combo-resource'
            ,fieldLabel: _('selections.resource')
            ,name: 'resource'
            ,hiddenName: 'resource'
            ,anchor: '100%'
            ,baseParams:{
                action: 'mgr/extra/getresources'
                ,sort: config.resourcesSort
                ,selection: config.selection
            }
        },{
            xtype: 'hidden'
            ,name: 'collection'
        }];
    }
});
Ext.reg('collections-window-selection',collections.window.Selection);

collections.window.ChangeParent = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        title: _('collections.children.changeparent')
        ,closeAction: 'close'
        ,isUpdate: false
        ,url: collections.config.connectorUrl
        ,action: 'mgr/resource/changechildparent'
        ,fields: this.getFields(config)
    });
    collections.window.ChangeParent.superclass.constructor.call(this,config);

    this.on('show',function() {
        var fld = this.fp.getForm().items.itemAt(0);
        fld.focus(false,200);
    },this);
};
Ext.extend(collections.window.ChangeParent,MODx.Window, {

    getFields: function(config) {
        return [{
            xtype: 'collections-combo-resource'
            ,fieldLabel: _('collections.children.parent')
            ,name: 'parent'
            ,hiddenName: 'parent'
            ,anchor: '100%'
            ,baseParams:{
                action: 'mgr/extra/getresources'
                ,sort: 'pagetitle:asc'
            }
        },{
            xtype: 'hidden'
            ,name: 'id'
        }];
    }
});
Ext.reg('collections-window-change-parent',collections.window.ChangeParent);

collections.window.QuickCreateResource = function(config) {
    config = config || {};
    this.ident = config.ident || 'qcr'+Ext.id();
    Ext.applyIf(config,{
        fields: [{
            xtype: 'modx-tabs'
            ,bodyStyle: { background: 'transparent' }
            ,border: true
            ,deferredRender: false
            ,autoHeight: false
            ,autoScroll: false
            ,anchor: '100% 100%'
            ,items: [{
                title: _('resource')
                ,layout: 'form'
                ,cls: 'modx-panel'
                ,autoHeight: false
                ,anchor: '100% 100%'
                ,labelWidth: 100
                ,items: [{
                    xtype: 'hidden'
                    ,name: 'id'
                },{
                    layout: 'column'
                    ,border: false
                    ,items: [{
                        columnWidth: .6
                        ,border: false
                        ,layout: 'form'
                        ,items: [{
                            xtype: 'textfield'
                            ,name: 'pagetitle'
                            ,id: 'modx-'+this.ident+'-pagetitle'
                            ,fieldLabel: _('resource_pagetitle')+'<span class="required">*</span>'
                            ,description: '<b>[[*pagetitle]]</b><br />'+_('resource_pagetitle_help')
                            ,anchor: '100%'
                            ,allowBlank: false
                        },{
                            xtype: 'textfield'
                            ,name: 'longtitle'
                            ,id: 'modx-'+this.ident+'-longtitle'
                            ,fieldLabel: _('resource_longtitle')
                            ,description: '<b>[[*longtitle]]</b><br />'+_('resource_longtitle_help')
                            ,anchor: '100%'
                        },{
                            xtype: 'textarea'
                            ,name: 'description'
                            ,id: 'modx-'+this.ident+'-description'
                            ,fieldLabel: _('resource_description')
                            ,description: '<b>[[*description]]</b><br />'+_('resource_description_help')
                            ,anchor: '100%'
                            ,grow: false
                            ,height: 50
                        },{
                            xtype: 'textarea'
                            ,name: 'introtext'
                            ,id: 'modx-'+this.ident+'-introtext'
                            ,fieldLabel: _('resource_summary')
                            ,description: '<b>[[*introtext]]</b><br />'+_('resource_summary_help')
                            ,anchor: '100%'
                            ,height: 50
                        }]
                    },{
                        columnWidth: .4
                        ,border: false
                        ,layout: 'form'
                        ,items: [{
                            xtype: 'modx-combo-template'
                            ,name: 'template'
                            ,id: 'modx-'+this.ident+'-template'
                            ,fieldLabel: _('resource_template')
                            ,description: '<b>[[*template]]</b><br />'+_('resource_template_help')
                            ,editable: false
                            ,anchor: '100%'
                            ,baseParams: {
                                action: 'element/template/getList'
                                ,combo: '1'
                                ,limit: 0
                            }
                            ,value: MODx.config.default_template
                        },{
                            xtype: 'textfield'
                            ,name: 'alias'
                            ,id: 'modx-'+this.ident+'-alias'
                            ,fieldLabel: _('resource_alias')
                            ,description: '<b>[[*alias]]</b><br />'+_('resource_alias_help')
                            ,anchor: '100%'
                        },{
                            xtype: 'textfield'
                            ,name: 'menutitle'
                            ,id: 'modx-'+this.ident+'-menutitle'
                            ,fieldLabel: _('resource_menutitle')
                            ,description: '<b>[[*menutitle]]</b><br />'+_('resource_menutitle_help')
                            ,anchor: '100%'
                        },{
                            xtype: 'textfield'
                            ,fieldLabel: _('resource_link_attributes')
                            ,description: '<b>[[*link_attributes]]</b><br />'+_('resource_link_attributes_help')
                            ,name: 'link_attributes'
                            ,id: 'modx-'+this.ident+'-attributes'
                            ,maxLength: 255
                            ,anchor: '100%'
                        },{
                            xtype: 'xcheckbox'
                            ,boxLabel: _('resource_hide_from_menus')
                            ,description: '<b>[[*hidemenu]]</b><br />'+_('resource_hide_from_menus_help')
                            ,hideLabel: true
                            ,name: 'hidemenu'
                            ,id: 'modx-'+this.ident+'-hidemenu'
                            ,inputValue: 1
                            ,checked: MODx.config.hidemenu_default == '1' ? 1 : 0
                        },{
                            xtype: 'xcheckbox'
                            ,name: 'published'
                            ,id: 'modx-'+this.ident+'-published'
                            ,boxLabel: _('resource_published')
                            ,description: '<b>[[*published]]</b><br />'+_('resource_published_help')
                            ,hideLabel: true
                            ,inputValue: 1
                            ,checked: MODx.config.publish_default == '1' ? 1 : 0
                        }]
                    }]
                },MODx.getQRContentField(this.ident,config.record.class_key)]
            },{
                id: 'modx-'+this.ident+'-settings'
                ,title: _('settings')
                ,layout: 'form'
                ,cls: 'modx-panel'
                ,autoHeight: true
                ,forceLayout: true
                ,labelWidth: 100
                ,defaults: {
                    autoHeight: true
                    ,border: false
                }
                ,items: this.getSettingFields(this.ident,config.record)
            }]
        }]
    });
    collections.window.QuickCreateResource.superclass.constructor.call(this,config);
};
Ext.extend(collections.window.QuickCreateResource, MODx.window.QuickCreateResource, {
    getSettingFields: function(id,va) {
        id = id || 'qur';
        return [{
            layout: 'column'
            ,border: false
            ,anchor: '100%'
            ,defaults: {
                labelSeparator: ''
                ,labelAlign: 'top'
                ,border: false
                ,layout: 'form'
            }
            ,items: [{
                columnWidth: .5
                ,items: [{
                    xtype: 'hidden'
                    ,name: 'context_key'
                    ,id: 'modx-'+id+'-context_key'
                    ,value: va['context_key']
                },{
                    xtype: 'hidden'
                    ,name: 'class_key'
                    ,id: 'modx-'+id+'-class_key'
                    ,value: va['class_key']
                },{
                    xtype: 'hidden'
                    ,name: 'publishedon'
                    ,id: 'modx-'+id+'-publishedon'
                    ,value: va['publishedon']
                },{
                    xtype: 'collections-combo-resource'
                    ,fieldLabel: _('collections.children.parent')
                    ,name: 'parent'
                    ,hiddenName: 'parent'
                    ,anchor: '100%'
                    ,baseParams:{
                        action: 'mgr/extra/getresources'
                        ,sort: 'pagetitle:asc'
                    }
                },{
                    xtype: 'modx-combo-class-derivatives'
                    ,fieldLabel: _('resource_type')
                    ,description: '<b>[[*class_key]]</b><br />'
                    ,name: 'class_key'
                    ,hiddenName: 'class_key'
                    ,id: 'modx-'+id+'-class-key'
                    ,anchor: '100%'
                    ,value: va['class_key'] != undefined ? va['class_key'] : 'modDocument'
                },{
                    xtype: 'modx-combo-content-type'
                    ,fieldLabel: _('resource_content_type')
                    ,description: '<b>[[*content_type]]</b><br />'+_('resource_content_type_help')
                    ,name: 'content_type'
                    ,hiddenName: 'content_type'
                    ,id: 'modx-'+id+'-type'
                    ,anchor: '100%'
                    ,value: va['content_type'] != undefined ? va['content_type'] : (MODx.config.default_content_type || 1)

                },{
                    xtype: 'modx-combo-content-disposition'
                    ,fieldLabel: _('resource_contentdispo')
                    ,description: '<b>[[*content_dispo]]</b><br />'+_('resource_contentdispo_help')
                    ,name: 'content_dispo'
                    ,hiddenName: 'content_dispo'
                    ,id: 'modx-'+id+'-dispo'
                    ,anchor: '100%'
                    ,value: va['content_dispo'] != undefined ? va['content_dispo'] : 0
                },{
                    xtype: 'numberfield'
                    ,fieldLabel: _('resource_menuindex')
                    ,description: '<b>[[*menuindex]]</b><br />'+_('resource_menuindex_help')
                    ,name: 'menuindex'
                    ,id: 'modx-'+id+'-menuindex'
                    ,width: 75
                    ,value: va['menuindex'] || 0
                }]
            },{
                columnWidth: .5
                ,items: [{
                    xtype: 'xdatetime'
                    ,fieldLabel: _('resource_publishedon')
                    ,description: '<b>[[*publishedon]]</b><br />'+_('resource_publishedon_help')
                    ,name: 'publishedon'
                    ,id: 'modx-'+id+'-publishedon'
                    ,allowBlank: true
                    ,dateFormat: MODx.config.manager_date_format
                    ,timeFormat: MODx.config.manager_time_format
                    ,startDay: parseInt(MODx.config.manager_week_start)
                    ,dateWidth: 153
                    ,timeWidth: 153
                    ,offset_time: MODx.config.server_offset_time
                    ,value: va['publishedon']
                },{
                    xtype: va['canpublish'] ? 'xdatetime' : 'hidden'
                    ,fieldLabel: _('resource_publishdate')
                    ,description: '<b>[[*pub_date]]</b><br />'+_('resource_publishdate_help')
                    ,name: 'pub_date'
                    ,id: 'modx-'+id+'-pub-date'
                    ,allowBlank: true
                    ,dateFormat: MODx.config.manager_date_format
                    ,timeFormat: MODx.config.manager_time_format
                    ,startDay: parseInt(MODx.config.manager_week_start)
                    ,dateWidth: 153
                    ,timeWidth: 153
                    ,offset_time: MODx.config.server_offset_time
                    ,value: va['pub_date']
                },{
                    xtype: va['canpublish'] ? 'xdatetime' : 'hidden'
                    ,fieldLabel: _('resource_unpublishdate')
                    ,description: '<b>[[*unpub_date]]</b><br />'+_('resource_unpublishdate_help')
                    ,name: 'unpub_date'
                    ,id: 'modx-'+id+'-unpub-date'
                    ,allowBlank: true
                    ,dateFormat: MODx.config.manager_date_format
                    ,timeFormat: MODx.config.manager_time_format
                    ,startDay: parseInt(MODx.config.manager_week_start)
                    ,dateWidth: 153
                    ,timeWidth: 153
                    ,offset_time: MODx.config.server_offset_time
                    ,value: va['unpub_date']
                },{
                    xtype: 'xcheckbox'
                    ,boxLabel: _('resource_folder')
                    ,description: _('resource_folder_help')
                    ,hideLabel: true
                    ,name: 'isfolder'
                    ,id: 'modx-'+id+'-isfolder'
                    ,inputValue: 1
                    ,checked: va['isfolder'] != undefined ? va['isfolder'] : false
                },{
                    xtype: 'xcheckbox'
                    ,boxLabel: _('resource_richtext')
                    ,description: _('resource_richtext_help')
                    ,hideLabel: true
                    ,name: 'richtext'
                    ,id: 'modx-'+id+'-richtext'
                    ,inputValue: 1
                    ,checked: va['richtext'] !== undefined ? (va['richtext'] ? 1 : 0) : (MODx.config.richtext_default == '1' ? 1 : 0)
                },{
                    xtype: 'xcheckbox'
                    ,boxLabel: _('resource_searchable')
                    ,description: _('resource_searchable_help')
                    ,hideLabel: true
                    ,name: 'searchable'
                    ,id: 'modx-'+id+'-searchable'
                    ,inputValue: 1
                    ,checked: va['searchable'] != undefined ? va['searchable'] : (MODx.config.search_default == '1' ? 1 : 0)
                    ,listeners: {'check': {fn:MODx.handleQUCB}}
                },{
                    xtype: 'xcheckbox'
                    ,boxLabel: _('resource_cacheable')
                    ,description: _('resource_cacheable_help')
                    ,hideLabel: true
                    ,name: 'cacheable'
                    ,id: 'modx-'+id+'-cacheable'
                    ,inputValue: 1
                    ,checked: va['cacheable'] != undefined ? va['cacheable'] : (MODx.config.cache_default == '1' ? 1 : 0)
                },{
                    xtype: 'xcheckbox'
                    ,name: 'clearCache'
                    ,id: 'modx-'+id+'-clearcache'
                    ,boxLabel: _('clear_cache_on_save')
                    ,description: _('clear_cache_on_save_msg')
                    ,hideLabel: true
                    ,inputValue: 1
                    ,checked: true
                },{
                    xtype: 'xcheckbox'
                    ,boxLabel: _('deleted')
                    ,description: _('resource_delete')
                    ,hideLabel: true
                    ,name: 'deleted'
                    ,id: 'modx-'+id+'-deleted'
                    ,inputValue: 1
                    ,checked: va['deleted'] != undefined ? va['deleted'] : 0
                },{
                    xtype: 'xcheckbox'
                    ,boxLabel: _('resource_alias_visible')
                    ,description: _('resource_alias_visible_help')
                    ,hideLabel: true
                    ,name: 'alias_visible'
                    ,id: 'modx-'+id+'-alias-visible'
                    ,inputValue: 1
                    ,checked: va['alias_visible'] != undefined ? va['alias_visible'] : 1
                },{
                    xtype: 'xcheckbox'
                    ,boxLabel: _('resource_uri_override')
                    ,description: _('resource_uri_override_help')
                    ,hideLabel: true
                    ,name: 'uri_override'
                    ,id: 'modx-'+id+'-uri-override'
                    ,value: 1
                    ,checked: parseInt(va['uri_override']) ? true : false
                    ,listeners: {'check': {fn:MODx.handleFreezeUri}}
                },{
                    xtype: 'textfield'
                    ,fieldLabel: _('resource_uri')
                    ,description: '<b>[[*uri]]</b><br />'+_('resource_uri_help')
                    ,name: 'uri'
                    ,id: 'modx-'+id+'-uri'
                    ,maxLength: 255
                    ,anchor: '100%'
                    ,value: va['uri'] || ''
                    ,hidden: !va['uri_override']
                }]
            }]
        }];
    }
});
Ext.reg('collections-quick-create-modResource',collections.window.QuickCreateResource);
