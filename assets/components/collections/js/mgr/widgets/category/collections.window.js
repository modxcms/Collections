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
