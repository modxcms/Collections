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

    this.on('show',function() {
        var fld = this.fp.getForm().items.itemAt(0);
        fld.focus(false,200);
    },this);
};
Ext.extend(Collections.window.Selection,MODx.Window, {

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
            }
        },{
            xtype: 'hidden'
            ,name: 'collection'
        }];
    }
});
Ext.reg('collections-window-selection',Collections.window.Selection);