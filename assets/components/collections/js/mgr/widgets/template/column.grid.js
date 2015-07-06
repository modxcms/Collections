Collections.grid.TemplateColumn = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        url: Collections.config.connectorUrl
        ,autosave: true
        ,save_action: 'mgr/template/column/updatefromgrid'
        ,baseParams: {
            action: 'mgr/template/column/getList'
            ,template: MODx.request.id
            ,sort: 'position'
            ,dir: 'asc'
        }
        ,fields: ['id', 'template', 'name', 'label', 'hidden', 'sortable', 'width', 'position', 'editor', 'renderer', 'php_renderer', 'sort_type']
        ,paging: true
        ,ddGroup: 'collectionChildDDGroup'
        ,enableDragDrop: true
        ,remoteSort: true
        ,emptyText: _('collections.template.column.none')
        ,columns: this.getColumns(config)
        ,tbar: [{
            text: _('collections.template.column.add')
            ,handler: this.createColumn
            ,scope: this
        }]
    });
    Collections.grid.TemplateColumn.superclass.constructor.call(this,config);

    this.on('render', this.registerGridDropTarget, this);
    this.on('beforedestroy', this.destroyScrollManager, this);
};
Ext.extend(Collections.grid.TemplateColumn,MODx.grid.Grid,{
    getMenu: function() {
        var m = [];

        m.push({
            text: _('collections.template.column.update')
            ,handler: this.updateColumn
        });

        var selected = this.getSelectionModel().getSelected();

        if (selected.data.name != 'id') {
            m.push({
                text: _('collections.template.column.remove')
                ,handler: this.removeColumn
            });
        }

        return m;
    }

    ,createColumn: function(btn,e) {
        var createColumn = MODx.load({
            xtype: 'collections-window-template-column'
            ,title: _('collections.template.column.add')
            ,record: {template: MODx.request.id}
            ,listeners: {
                'success': {fn:function() { this.refresh(); },scope:this}
            }
        });

        createColumn.show(e.target);
    }

    ,updateColumn: function(btn,e) {
        var updateColumn = MODx.load({
            xtype: 'collections-window-template-column'
            ,title: _('collections.template.column.update')
            ,action: 'mgr/template/column/update'
            ,isUpdate: true
            ,record: this.menu.record
            ,listeners: {
                'success': {fn:function() { this.refresh(); },scope:this}
            }
        });

        updateColumn.fp.getForm().reset();
        updateColumn.fp.getForm().setValues(this.menu.record);
        updateColumn.show(e.target);
    }

    ,removeColumn: function(btn,e) {
        if (!this.menu.record) return false;

        MODx.msg.confirm({
            title: _('collections.template.column.remove')
            ,text: _('collections.template.column.remove_confirm')
            ,url: this.config.url
            ,params: {
                action: 'mgr/template/column/remove'
                ,id: this.menu.record.id
            }
            ,listeners: {
                'success': {fn:function(r) { this.refresh(); },scope:this}
            }
        });

        return true;
    }

    ,getColumns: function(config) {
        return [{
            header: _('id')
            ,dataIndex: 'id'
            ,sortable: true
            ,hidden: true
            ,width: 40
        },{
            header: _('collections.template.column.label')
            ,dataIndex: 'label'
            ,sortable: true
            ,editor: {xtype: 'textfield'}
            ,width: 100
        },{
            header: _('collections.template.column.name')
            ,dataIndex: 'name'
            ,sortable: true
            ,editor: {xtype: 'textfield'}
            ,width: 100
        },{
            header: _('collections.template.column.hidden')
            ,dataIndex: 'hidden'
            ,sortable: true
            ,editor: {xtype: 'modx-combo-boolean', renderer: true}
            ,width: 50
        },{
            header: _('collections.template.column.sortable')
            ,dataIndex: 'sortable'
            ,sortable: true
            ,editor: {xtype: 'modx-combo-boolean', renderer: true}
            ,width: 50
        },{
            header: _('collections.template.column.width')
            ,dataIndex: 'width'
            ,sortable: true
            ,editor: {xtype: 'numberfield', allowNegative: false, allowDecimals: false}
            ,width: 40
        },{
            header: _('collections.template.column.editor')
            ,dataIndex: 'editor'
            ,sortable: true
            ,editor: {xtype: 'textarea'}
            ,width: 100
        },{
            header: _('collections.template.column.renderer')
            ,dataIndex: 'renderer'
            ,sortable: true
            ,editor: {xtype: 'textfield'}
            ,width: 100
            ,renderer: Collections.renderer.qtip
        },{
            header: _('collections.template.column.position')
            ,dataIndex: 'position'
            ,sortable: true
            ,editor: {xtype: 'numberfield', allowNegative: false, allowDecimals: false}
            ,width: 50
        }];
    }

    ,registerGridDropTarget: function() {

        var ddrow = new Ext.ux.dd.GridReorderDropTarget(this, {
            copy: false
            ,sortCol: 'position'
            ,listeners: {
                'beforerowmove': function(objThis, oldIndex, newIndex, records) {
                }

                ,'afterrowmove': function(objThis, oldIndex, newIndex, records) {
                    MODx.Ajax.request({
                        url: Collections.config.connectorUrl
                        ,params: {
                            action: 'mgr/template/column/ddreorder'
                            ,idItem: records.pop().id
                            ,oldIndex: oldIndex
                            ,newIndex: newIndex
                            ,template: MODx.request.id
                        }
                        ,listeners: {
                            'success': {
                                fn: function(r) {
                                    this.target.grid.refresh();
                                },scope: this
                            }
                        }
                    });
                }

                ,'beforerowcopy': function(objThis, oldIndex, newIndex, records) {
                }

                ,'afterrowcopy': function(objThis, oldIndex, newIndex, records) {
                }
            }
        });

        Ext.dd.ScrollManager.register(this.getView().getEditorParent());
    }
    
    ,destroyScrollManager: function() {
        Ext.dd.ScrollManager.unregister(this.getView().getEditorParent());
    }

    ,getDragDropText: function(){
        if (this.config.baseParams.sort != 'position') {
            if (this.store.sortInfo == undefined || this.store.sortInfo.field != 'position') {
                return _('collections.err.bad_sort_column', {column: 'position'});
            }
        }

        return _('collections.global.change_order', {child: this.selModel.selections.items[0].data.name});
    }

});
Ext.reg('collections-grid-template-column',Collections.grid.TemplateColumn);