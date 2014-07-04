Collections.panel.Category = function(config) {
    config = config || {};
    // Hotfix, try to find better solution
    MODx.config.confirm_navigation = 0;
    Collections.panel.Category.superclass.constructor.call(this,config);
};
Ext.extend(Collections.panel.Category,MODx.panel.Resource,{
    getPageHeader: function(config) {
        config = config || {record:{}};
        return {
            html: '<h2>'+_('collections.container_new')+'</h2>'
            ,id: 'modx-resource-header'
            ,cls: 'modx-page-header'
            ,border: false
            ,forceLayout: true
            ,anchor: '100%'
        };
    }

    ,getFields: function(config) {
        var fields = Collections.panel.Category.superclass.getFields.call(this,config);

        var tabs = fields.filter(function (row) {
            if(row.id == 'modx-resource-tabs') {
                return row;
            } else {
                return false;
            }
        });

        if (tabs != false && tabs[0]) {
        	if (config.mode == 'update') {
	            tabs[0].items.unshift({
	                title: _('collections.children')
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
	        }
            
        }

        return fields;
    }

    ,getCollectionsChildrenTab: function(config) {
        return [{
            'xtype': 'collections-grid-children'
            ,url: Collections.connectorUrl
            ,anchor: '100%'
        }];
    }

});
Ext.reg('collections-panel-category',Collections.panel.Category);