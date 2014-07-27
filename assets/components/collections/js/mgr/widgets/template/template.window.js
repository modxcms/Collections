Collections.window.TemplateDuplicate = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        title: _('collections.template.duplicate')
        ,closeAction: 'close'
        ,isUpdate: false
        ,url: Collections.config.connectorUrl
        ,action: 'mgr/template/duplicate'
        ,fields: this.getFields(config)
    });
    Collections.window.TemplateDuplicate.superclass.constructor.call(this,config);
};
Ext.extend(Collections.window.TemplateDuplicate,MODx.Window, {
    getFields: function(config) {
        return [{
            xtype: 'textfield'
            ,name: 'id'
            ,anchor: '100%'
            ,hidden: true
        },{
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
            ,allowBlank: true
        }];
    }
});
Ext.reg('collections-window-template-duplicate',Collections.window.TemplateDuplicate);