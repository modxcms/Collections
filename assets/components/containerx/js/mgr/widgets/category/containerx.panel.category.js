ContainerX.panel.Category = function(config) {
    config = config || {};
    ContainerX.panel.Category.superclass.constructor.call(this,config);
};
Ext.extend(ContainerX.panel.Category,MODx.panel.Resource,{
    getPageHeader: function(config) {
        config = config || {record:{}};
        return {
            html: '<h2>'+_('containerx.container_new')+'</h2>'
            ,id: 'modx-resource-header'
            ,cls: 'modx-page-header'
            ,border: false
            ,forceLayout: true
            ,anchor: '100%'
        };
    }

    ,getFields: function(config) {
        var it = [];

        if (config.mode == 'update') {
            it.push({
                title: _('containerx.children')
                ,id: 'containerx-category-resources'
                ,cls: 'modx-resource-tab'
                ,layout: 'form'
                ,labelAlign: 'top'
                ,labelSeparator: ''
                ,bodyCssClass: 'tab-panel-wrapper main-wrapper'
                ,autoHeight: true
                ,defaults: {
                    border: false
                    ,msgTarget: 'under'
                    ,width: 400
                }
                ,items: this.getLocationsTab(config)
            });
        }

        it.push({
            title: _(this.classLexiconKey)
            ,id: 'modx-resource-settings'
            ,cls: 'modx-resource-tab'
            ,layout: 'form'
            ,labelAlign: 'top'
            ,labelSeparator: ''
            ,bodyCssClass: 'tab-panel-wrapper main-wrapper'
            ,autoHeight: true
            ,defaults: {
                border: false
                ,msgTarget: 'under'
                ,width: 400
            }
            ,items: this.getMainFields(config)
        });

        it.push({
            id: 'modx-page-settings'
            ,title: _('settings')
            ,cls: 'modx-resource-tab'
            ,layout: 'form'
            ,forceLayout: true
            ,deferredRender: false
            ,labelWidth: 200
            ,bodyCssClass: 'main-wrapper'
            ,autoHeight: true
            ,defaults: {
                border: false
                ,msgTarget: 'under'
            }
            ,items: this.getSettingFields(config)
        });
        if (config.show_tvs && MODx.config.tvs_below_content != 1) {
            it.push(this.getTemplateVariablesPanel(config));
        }
        if (MODx.perm.resourcegroup_resource_list == 1) {
            it.push(this.getAccessPermissionsTab(config));
        }
        var its = [];
        its.push(this.getPageHeader(config),{
            id:'modx-resource-tabs'
            ,xtype: 'modx-tabs'
            ,forceLayout: true
            ,deferredRender: false
            ,collapsible: true
            ,animCollapse: false
            ,itemId: 'tabs'
            ,items: it
            ,listeners: {
                'tabchange': function(tabs, tab) {
                    if (tab.id == 'containerx-category-resources') {
                        Ext.getCmp('modx-resource-content').hide();
                    } else {
                        Ext.getCmp('modx-resource-content').show();
                    }
                }
            }
        });
        var ct = this.getContentField(config);
        if (ct) {
            its.push({
                title: _('resource_content')
                ,id: 'modx-resource-content'
                ,layout: 'form'
                ,bodyCssClass: 'main-wrapper'
                ,autoHeight: true
                ,collapsible: true
                ,animCollapse: false
                ,hideMode: 'offsets'
                ,items: ct
                ,style: 'margin-top: 10px'
            });
        }
        if (MODx.config.tvs_below_content == 1) {
            var tvs = this.getTemplateVariablesPanel(config);
            its.push(tvs);
        }
        return its;
    }

    ,getLocationsTab: function(config) {
        return [{
            'xtype': 'containerx-grid-children'
            ,url: ContainerX.connectorUrl
            ,anchor: '100%'
        }];
    }

});
Ext.reg('containerx-panel-category',ContainerX.panel.Category);