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

Collections.window.TemplateImport = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        title: _('collections.template.import')
        ,closeAction: 'close'
        ,isUpdate: false
        ,fileUpload: true
        ,url: Collections.config.connectorUrl
        ,action: 'mgr/template/import'
        ,fields: this.getFields(config)
        ,autoHeight: true
        ,saveBtnText: _('collections.global.import')
    });
    Collections.window.TemplateImport.superclass.constructor.call(this,config);
};
Ext.extend(Collections.window.TemplateImport,MODx.Window, {
    getFields: function(config) {
        return [{
            xtype: 'fileuploadfield'
            ,fieldLabel: _('collections.template.name')
            ,name: 'file'
            ,anchor: '100%'
            ,allowBlank: false
            ,listeners: {
                render: function(){
                    this.fileInput.dom.setAttribute('accept', '.json');                                 
                },
                fileselected: {
                    fn: function(field, value){
                        try {
                            var text = field.fileInput.dom.files[0];
                            var reader = new FileReader();
                            reader.readAsText(text);
                            
                            var window = this;
                            
                            reader.onload = function(event) {
                                var templates = JSON.parse(event.target.result);
                                var checkboxGroup = Ext.getCmp('template-checkbox-group');
                                if (checkboxGroup) {
                                    checkboxGroup.destroy();
                                }
                                
                                checkboxGroup = {
                                    xtype: 'checkboxgroup',
                                    id: 'template-checkbox-group',
                                    fieldLabel: _('collections.template.import'),
                                    itemCls: 'x-check-group-alt',
                                    columns: 3,
                                    items: []
                                };
                                
                                Ext.each(templates, function(template){
                                    checkboxGroup.items.push({
                                        boxLabel: template.name, 
                                        name: 'template[]',
                                        value: template.name,
                                        checked: true,
                                        listeners: {
                                            render: function() {
                                                this.el.dom.setAttribute('value', this.value);
                                            }
                                        }
                                    });             
                                });
                            
                                window.fp.add(checkboxGroup);
                                window.doLayout();
                            };
                        } catch (err) {
                            var checkboxGroup = Ext.getCmp('template-checkbox-group');
                            if (checkboxGroup) {
                                checkboxGroup.destroy();
                            }
                        }
                    },
                    scope: this
                }
            }
        }];
    }
});
Ext.reg('collections-window-template-import', Collections.window.TemplateImport);