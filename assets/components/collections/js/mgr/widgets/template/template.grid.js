collections.grid.Template = function(config) {
    config = config || {};

    this.sm = new Ext.grid.CheckboxSelectionModel({
        listeners: {
            rowselect: {
                fn: function (sm, rowIndex, record) {
                    this.rememberRow(record);
                }, scope: this
            },
            rowdeselect: {
                fn: function (sm, rowIndex, record) {
                    this.forgotRow(record);
                }
                ,scope: this
            }
        }
    });
    
    Ext.applyIf(config,{
        title: _('collections.collections')
        ,url: collections.config.connectorUrl
        ,baseParams: {
            action: 'mgr/template/getList'
            ,'template': 1
        }
        ,save_action: 'mgr/template/updatefromgrid'
        ,autosave: true
        ,preventSaveRefresh: false
        ,fields: ['id','name', 'description', 'global_template', 'default_for_templates']
        ,paging: true
        ,remoteSort: true
        ,emptyText: _('collections.template.none')
        ,sm: this.sm
        ,columns: [this.sm,{
            header: _('id')
            ,dataIndex: 'id'
            ,sortable: true
            ,hidden: true
        },{
            header: _('collections.template.name')
            ,dataIndex: 'name'
            ,sortable: true
            ,width: 80
            ,editor: {xtype: 'textfield'}
        },{
            header: _('collections.template.description')
            ,dataIndex: 'description'
            ,sortable: true
            ,width: 100
            ,editor: {xtype: 'textfield'}
        },{
            header: 'Default for Templates'
            ,dataIndex: 'default_for_templates'
            ,sortable: false
            ,width: 60
            ,renderer: function(value, metaData, record, rowIndex, colIndex, store) {
                metaData.attr = 'ext:qtip="' + value.join('<br />') + '"';
                
                return value;
            }
        },{
            header: _('collections.template.global_template')
            ,dataIndex: 'global_template'
            ,sortable: true
            ,width: 30
            ,editor: {xtype: 'modx-combo-boolean', renderer: true}
        }]
        ,tbar: [{
            text: _('collections.template.add')
            ,handler: this.createTemplate
            ,scope: this
        },{
            text: _('collections.template.import')
            ,handler: this.importTemplate
            ,scope: this
        }]
    });
    collections.grid.Template.superclass.constructor.call(this,config);

    this.getView().on('refresh', this.refreshSelection, this);
};
Ext.extend(collections.grid.Template,MODx.grid.Grid,{

    selectedRecords: []

    ,rememberRow: function(record) {
        if(this.selectedRecords.indexOf(record.id) == -1){
            this.selectedRecords.push(record.id);
        }
    }
    
    ,forgotRow: function(record){
        this.selectedRecords.remove(record.id);
    }
    
    ,refreshSelection: function() {
        var rowsToSelect = [];
        Ext.each(this.selectedRecords, function(item){
            rowsToSelect.push(this.store.indexOfId(item));
        },this);
    
        this.getSelectionModel().selectRows(rowsToSelect);
    }
    
    ,getSelectedAsList: function(){
        return this.selectedRecords.join();
    }

    ,getMenu: function() {
        var m = [];

        m.push({
            text: _('collections.template.update')
            ,handler: this.editTemplate
        });

        m.push({
            text: _('collections.template.duplicate')
            ,handler: this.duplicateTemplate
        });

        m.push('-');

        m.push({
            text: (this.selectedRecords.length > 1) ? _('collections.template.export_more') : _('collections.template.export')
            ,handler: this.exportTemplate
        });
        
        m.push('-');

        m.push({
            text: _('collections.template.remove')
            ,handler: this.removeTemplate
        });
        return m;
    }

    ,editTemplate: function() {
        MODx.loadPage(MODx.action['collections:index'], 'action=template/update&id='+ this.menu.record.id);
    }

    ,createTemplate: function() {
        MODx.loadPage(MODx.action['collections:index'], 'action=template/create');
    }
    
    ,exportTemplate: function(){
        MODx.loadPage(MODx.action['collections:index'], 'action=template/export&ids=' + this.getSelectedAsList());                     
    }

    ,removeTemplate: function(btn,e) {
        if (!this.menu.record) return false;

        MODx.msg.confirm({
            title: _('collections.template.remove')
            ,text: _('collections.template.remove_confirm')
            ,url: this.config.url
            ,params: {
                action: 'mgr/template/remove'
                ,id: this.menu.record.id
            }
            ,listeners: {
                'success': {fn:function(r) { this.refresh(); },scope:this}
            }
        });

        return true;
    }

    ,duplicateTemplate: function(btn,e) {
        if (!this.menu.record) return false;

        var r = {};

        r.name = 'Copy of ' + this.menu.record.name;
        r.id = this.menu.record.id;

        var updateColumn = MODx.load({
            xtype: 'collections-window-template-duplicate'
            ,record: r
            ,listeners: {
                'success': {fn:function() { this.refresh(); },scope:this}
            }
        });

        updateColumn.fp.getForm().reset();
        updateColumn.fp.getForm().setValues(r);
        updateColumn.show(e.target);

        return true;
    }
    
    ,importTemplate: function(btn, e) {
        var importWindow = MODx.load({
            xtype: 'collections-window-template-import'
            ,listeners: {
                'success': {fn:function() { this.refresh(); },scope:this}
            }
        });

        importWindow.show(e.target);

        return true; 
    }

});
Ext.reg('collections-grid-template',collections.grid.Template);