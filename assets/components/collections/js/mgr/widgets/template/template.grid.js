Collections.grid.Template = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        title: _('collections.collections')
        ,url: Collections.config.connectorUrl
        ,baseParams: {
            action: 'mgr/template/getList'
            ,'template': 1
        }
        ,save_action: 'mgr/template/updatefromgrid'
        ,autosave: true
        ,preventSaveRefresh: false
        ,fields: ['id','name', 'description', 'global_template']
        ,paging: true
        ,remoteSort: true
        ,emptyText: _('collections.template.none')
        ,columns: [{
            header: _('collections.template.name')
            ,dataIndex: 'name'
            ,sortable: true
            ,width: 100
            ,editor: {xtype: 'textfield'}
        },{
            header: _('collections.template.description')
            ,dataIndex: 'description'
            ,sortable: true
            ,width: 150
            ,editor: {xtype: 'textfield'}
        },{
            header: _('collections.template.global_template')
            ,dataIndex: 'global_template'
            ,sortable: true
            ,width: 40
            ,editor: {xtype: 'modx-combo-boolean', renderer: true}
        }]
        ,tbar: [{
            text: _('collections.template.add')
            ,handler: this.createTemplate
            ,scope: this
        }]
    });
    Collections.grid.Template.superclass.constructor.call(this,config);
};
Ext.extend(Collections.grid.Template,MODx.grid.Grid,{
    getMenu: function() {
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

});
Ext.reg('collections-grid-template',Collections.grid.Template);