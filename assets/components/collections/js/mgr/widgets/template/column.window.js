Collections.window.TemplateColumn = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        title: _('collections.template.column.add')
        ,closeAction: 'close'
        ,isUpdate: false
        ,width: 600
        ,autoHeight: true
        ,url: Collections.config.connectorUrl
        ,action: 'mgr/template/column/create'
        ,fields: this.getFields(config)
    });
    Collections.window.TemplateColumn.superclass.constructor.call(this,config);
};
Ext.extend(Collections.window.TemplateColumn,MODx.Window, {
    getLeftColumnFields: function (config) {
        return [{
            xtype: 'textfield'
            ,fieldLabel: _('collections.template.column.label')
            ,name: 'label'
            ,anchor: '100%'
        },{
            xtype: 'textfield'
            ,fieldLabel: _('collections.template.column.name')
            ,name: 'name'
            ,anchor: '100%'
            ,allowBlank: false
            ,readOnly: (config.record && config.record.name == 'id')
            ,cls: (config.record && config.record.name == 'id') ? 'x-item-disabled' : ''
        },{
            xtype: 'numberfield'
            ,allowNegative: false
            ,allowDecimals: false
            ,fieldLabel: _('collections.template.column.width')
            ,name: 'width'
            ,anchor: '100%'
        },{
            xtype: 'numberfield'
            ,allowNegative: false
            ,allowDecimals: false
            ,fieldLabel: _('collections.template.column.position')
            ,name: 'position'
            ,anchor: '100%'
        }];
    },
    getRightColumnFields: function (config) {
        return [{
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
            xtype: 'textfield'
            ,fieldLabel: _('collections.template.column.php_renderer')
            ,name: 'php_renderer'
            ,anchor: '100%'
        },{
            html: '<br />'
        },{
            layout: 'column'
            ,border: false
            ,anchor: '100%'
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
                    xtype: 'xcheckbox'
                    ,fieldLabel: _('collections.template.column.hidden')
                    ,name: 'hidden'
                    ,anchor: '100%'
                    ,allowBlank: false
                }]
            },{
                columnWidth: .5
                ,border: false
                ,defaults: {
                    msgTarget: 'under'
                    ,anchor: '100%'
                }
                ,items: [{
                    xtype: 'xcheckbox'
                    ,fieldLabel: _('collections.template.column.sortable')
                    ,name: 'sortable'
                    ,anchor: '100%'
                    ,allowBlank: false
                    ,listeners: {
                        check: function(t, checked) {
                            var els = this.find('name', 'sort_type');
                            if (els.length == 1) {
                                if (checked) {
                                    els[0].show();
                                } else {
                                    els[0].hide();
                                }
                            }
                        }
                        ,scope: this
                    }
                }]
            }]
        },{
            xtype: 'collections-combo-sort-type'
            ,fieldLabel: _('collections.template.column.sort_type')
            ,name: 'sort_type'
            ,hiddenName: 'sort_type'
            ,hidden: config.record == undefined || !config.record.sortable
            ,anchor: '100%'
            ,hideMode:'offsets'
        }];
    },
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
            layout: 'column'
            ,border: false
            ,anchor: '100%'
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
                ,items: this.getLeftColumnFields(config)
            },{
                columnWidth: .5
                ,border: false
                ,defaults: {
                    msgTarget: 'under'
                    ,anchor: '100%'
                }
                ,items: this.getRightColumnFields(config)
            }]
        }];
    }
});
Ext.reg('collections-window-template-column',Collections.window.TemplateColumn);