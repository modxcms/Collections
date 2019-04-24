collections.panel.Category = function(config) {
    config = config || {};
    // Hotfix, try to find better solution
    MODx.config.confirm_navigation = 0;
    collections.panel.Category.superclass.constructor.call(this,config);
};
Ext.extend(collections.panel.Category,MODx.panel.Resource,{
    getPageHeader: function(config) {
        config = config || {record:{}};
        return {
            html: _('collections.container_new')
            ,id: 'modx-resource-header'
            ,xtype: 'modx-header'
        };
    }

    ,getContentField: function(config) {
        if (collections.template.content_place == 'none') return false;
        if (collections.template.content_place == 'in-tab') return false;

        return collections.panel.Category.superclass.getContentField.call(this,config);
    }

    ,getFields: function(config) {
        var fields = collections.panel.Category.superclass.getFields.call(this,config);

        var tabs = fields.filter(function (row) {
            if(row.id == 'modx-resource-tabs') {
                return row;
            } else {
                return false;
            }
        });


        if (tabs != false && tabs[0]) {
        	if (config.mode == 'update') {

                if (collections.template.content_place == 'original-except-children') {
                    tabs[0].listeners = {
                        tabchange: function(t, tab) {
                            if (tab.id == 'collections-category-resources') {
                                Ext.getCmp('modx-resource-content').hide();
                            } else {
                                Ext.getCmp('modx-resource-content').show();
                            }
                        }
                    };
                }

	            tabs[0].items.unshift({
	                title: (_(collections.template.tab_label) == undefined) ? collections.template.tab_label : _(collections.template.tab_label)
	                ,id: 'collections-category-resources'
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
	                ,items: this.getCollectionsChildrenTab(config)
	            });

                if (collections.template.content_place == 'in-tab') {
                    tabs[0].items.splice(2,0,{
                        title: _('resource_content')
                        ,layout: 'form'
                        ,bodyCssClass: 'main-wrapper'
                        ,id: 'modx-resource-content'
                        ,autoHeight: true
                        ,hideMode: 'offsets'
                        ,items: collections.panel.Category.superclass.getContentField.call(this,config)
                    });
                }
	        }
            
        }

        return fields;
    }

    ,getSettingFields: function(config) {
        return [{
            xtype: 'modx-vtabs'
            ,deferredRender: false
            ,ctCls: 'collections-setting-vtab'
            ,items: [{
                title: _('resource')
                ,deferredRender: false
                ,items: collections.panel.Category.superclass.getSettingFields.call(this,config)
            },{
                title: _('collections')
                ,deferredRender: false
                ,items: [{
                    xtype: 'collections-combo-collections-template'
                    ,fieldLabel: _('collections.template.template')
                    ,name: 'collections_template'
                    ,hiddenName: 'collections_template'
                    ,anchor: '100%'
                    ,url: collections.connectorUrl
                    ,baseParams: {
                        action: 'mgr/template/getlist'
                        ,addEmpty: 1
                    }
                }]
            }]
        }];
    }

    ,getCollectionsChildrenTab: function(config) {
        var items = [];
        if (collections.template.selection) {
            items.push({
                html: '<p>Attention! Those are linked Resources. If you change anything, it will appear in the original Resource as well.</p>'
                ,border: false
                ,bodyCssClass: 'panel-desc'
                ,anchor: '100%'
            });
        }

        items.push({
            'xtype': 'collections-grid-children'
            ,url: collections.connectorUrl
            ,anchor: '100%'
            ,resourcePanel: this
        });

        return items;
    }

});
Ext.reg('collections-panel-category',collections.panel.Category);