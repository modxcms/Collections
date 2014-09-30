Collections.window.Selection = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        title: _('collections.selection.create')
        ,closeAction: 'close'
        ,isUpdate: false
        ,url: Collections.config.connectorUrl
        ,action: 'mgr/selection/create'
        ,fields: this.getFields(config)
    });
    Collections.window.Selection.superclass.constructor.call(this,config);
};
Ext.extend(Collections.window.Selection,MODx.Window, {

    getFields: function(config) {
        return [{
            xtype: 'textfield'
            ,name: 'collection'
            ,hidden: true
        },{
            xtype: 'collections-combo-resource'
            ,fieldLabel: _('collections.selection.resource')
            ,name: 'resource'
            ,hiddenName: 'resource'
            ,anchor: '100%'
        }];
    }
});
Ext.reg('collections-window-selection',Collections.window.Selection);