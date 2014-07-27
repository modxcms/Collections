Collections.window.TemplateColumn = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        title: _('collections.template.column.add')
        ,closeAction: 'close'
        ,isUpdate: false
        ,url: Collections.config.connectorUrl
        ,action: 'mgr/template/column/create'
        ,fields: this.getFields(config)
    });
    Collections.window.TemplateColumn.superclass.constructor.call(this,config);
};
Ext.extend(Collections.window.TemplateColumn,MODx.Window, {
    getFields: function(config) {
        return [{
            xtype: 'textfield'
            ,name: 'id'
            ,anchor: '100%'
            ,hidden: true
        },{
            xtype: 'textfield'
            ,name: 'template'
            ,hidden: true
            ,allowBlank: false
        },{
            xtype: 'textfield'
            ,fieldLabel: _('collections.template.column.label')
            ,name: 'label'
            ,anchor: '100%'
            ,allowBlank: false
        },{
            xtype: 'textfield'
            ,fieldLabel: _('collections.template.column.name')
            ,name: 'name'
            ,anchor: '100%'
            ,allowBlank: false
            ,readOnly: (config.record && config.record.name == 'id')
            ,cls: (config.record && config.record.name == 'id') ? 'x-item-disabled' : ''
        },{
            xtype: 'xcheckbox'
            ,fieldLabel: _('collections.template.column.hidden')
            ,name: 'hidden'
            ,anchor: '100%'
            ,allowBlank: false
        },{
            xtype: 'xcheckbox'
            ,fieldLabel: _('collections.template.column.sortable')
            ,name: 'sortable'
            ,anchor: '100%'
            ,allowBlank: false
        },{
            xtype: 'numberfield'
            ,allowNegative: false
            ,allowDecimals: false
            ,fieldLabel: _('collections.template.column.width')
            ,name: 'width'
            ,anchor: '100%'
        },{
            xtype: 'textfield'
            ,fieldLabel: _('collections.template.column.editor')
            ,name: 'editor'
            ,anchor: '100%'
        },{
            xtype: 'textfield'
            ,fieldLabel: _('collections.template.column.renderer')
            ,name: 'renderer'
            ,anchor: '100%'
        },{
            xtype: 'numberfield'
            ,allowNegative: false
            ,allowDecimals: false
            ,fieldLabel: _('collections.template.column.position')
            ,name: 'position'
            ,anchor: '100%'
        }];
    }
});
Ext.reg('collections-window-template-column',Collections.window.TemplateColumn);