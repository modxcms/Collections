Collections.grid.ContainerCollections = function(config) {
    config = config || {};
    this.sm = new Ext.grid.CheckboxSelectionModel();
    Ext.applyIf(config,{
        id: 'collections-grid-container-collections'
        ,title: _('collections.collections')
        ,url: Collections.connectorUrl
        ,autosave: true
        ,save_action: 'mgr/resource/updatefromgrid'
        ,ddGroup: 'collectionChildDDGroup'
        ,enableDragDrop: false
        ,baseParams: {
            action: 'mgr/resource/getlist'
            ,parent: MODx.request.id
            ,sort: Collections.template.sort.field
            ,dir: Collections.template.sort.dir
        }
        ,fields: Collections.template.fields
        ,paging: true
        ,remoteSort: true
        ,pageSize: Collections.template.pageSize
        ,cls: 'collections-grid'
        ,bodyCssClass: 'grid-with-buttons'
        ,sm: this.sm
        ,emptyText: _('collections.children.none')
        ,columns: this.getColumns(config)
        ,tbar: this.getTbar(config)
    });
    Collections.grid.ContainerCollections.superclass.constructor.call(this,config);
    this.on('rowclick',MODx.fireResourceFormChange);
    this.on('click', this.handleButtons, this);

    if (Collections.template.allowDD) {
        this.on('render', this.registerGridDropTarget, this);
        this.on('beforedestroy', this.destroyScrollManager, this);
    }
};
Ext.extend(Collections.grid.ContainerCollections,MODx.grid.Grid,{
    getMenu: function() {
        var m = [];
        if (!this.menu.record) return m;

        var addDelimiter = false;
        Ext.each(Collections.template.context_menu, function(key) {
            if (key == '-') {
                addDelimiter = true;
                return true;
            }

            if (this.menu.record.menu_actions[key] != undefined) {
                if (addDelimiter == true) {
                    m.push('-');
                    addDelimiter = false;
                }

                m.push({
                    text: _('collections.children.' + key)
                    ,handler: 'this.' + key + 'Child'
                });
            }

        }, this);

        return m;
    }

    ,getColumns: function(config) {
        var columns = Collections.template.columns;

        if (Collections.template.bulkActions) {
            columns.unshift(this.sm);
        }

        return columns;
    }

    ,getTbar: function(config) {
        var items = [];

        if (Collections.template.resource_type_selection && Collections.template.resourceDerivatives.length > 0) {
            var resourceDerivatives = [];

            Ext.each(Collections.template.resourceDerivatives, function(item){
                resourceDerivatives.push({
                    text: item.name
                    ,derivative: item.id
                    ,handler: this.createDerivativeChild
                    ,scope: this
                });
            }, this);

            items.push({
                text: (_(Collections.template.button_label) == undefined) ? Collections.template.button_label : _(Collections.template.button_label)
                ,handler: this.createChild
                ,xtype: 'splitbutton'
                ,scope: this
                ,menu: resourceDerivatives
            });
        } else {
            items.push({
                text: (_(Collections.template.button_label) == undefined) ? Collections.template.button_label : _(Collections.template.button_label)
                ,handler: this.createChild
                ,scope: this
            });
        }


        if (Collections.template.bulkActions) {
            items.push({
                text: _('bulk_actions')
                ,xtype: 'splitbutton'
                ,menu: [{
                    text: _('collections.children.publish_multiple')
                    ,handler: this.publishSelected
                    ,scope: this
                },{
                    text: _('collections.children.unpublish_multiple')
                    ,handler: this.unpublishSelected
                    ,scope: this
                },'-',{
                    text: _('collections.children.delete_multiple')
                    ,handler: this.deleteSelected
                    ,scope: this
                },{
                    text: _('collections.children.undelete_multiple')
                    ,handler: this.undeleteSelected
                    ,scope: this
                }]
            });
        }

        items.push('->',{
            xtype: 'collections-combo-filter-status'
            ,id: 'collections-grid-filter-status'
            ,value: ''
            ,listeners: {
                'select': {fn:this.filterStatus,scope:this}
            }
        },{
            xtype: 'textfield'
            ,name: 'search'
            ,id: 'collections-child-search'
            ,emptyText: _('search_ellipsis')
            ,listeners: {
                'change': {fn: this.search, scope: this}
                ,'render': {fn: function(cmp) {
                    new Ext.KeyMap(cmp.getEl(), {
                        key: Ext.EventObject.ENTER
                        ,fn: function() {
                            this.fireEvent('change',this.getValue());
                            this.blur();
                            return true;}
                        ,scope: cmp
                    });
                },scope:this}
            }
        },{
            xtype: 'button'
            ,id: 'modx-filter-clear'
            ,text: _('filter_clear')
            ,listeners: {
                'click': {fn: this.clearFilter, scope: this}
            }
        });

        return items;
    }

    ,filterStatus: function(cb,nv,ov) {
        this.getStore().baseParams.filter = Ext.isEmpty(nv) || Ext.isObject(nv) ? cb.getValue() : nv;
        this.getBottomToolbar().changePage(1);
        this.refresh();
        return true;
    }

    ,search: function(tf,newValue,oldValue) {
        var nv = newValue || tf;
        this.getStore().baseParams.query = Ext.isEmpty(nv) || Ext.isObject(nv) ? '' : nv;
        this.getBottomToolbar().changePage(1);
        this.refresh();
        return true;
    }

    ,clearFilter: function() {
        this.getStore().baseParams = {
            action: 'mgr/resource/getList'
            ,'parent': MODx.request.id
        };
        Ext.getCmp('collections-child-search').reset();
        Ext.getCmp('collections-grid-filter-status').reset();
        this.getBottomToolbar().changePage(1);
    }

    ,editChild: function(btn,e) {
        MODx.loadPage(MODx.request.a, 'id=' + this.menu.record.id);
    }

    ,createChild: function(btn,e) {
        var template = '';
        if (Collections.template.children.template != null) {
            template = '&template=' + Collections.template.children.template;
        }

        MODx.loadPage(MODx.action['resource/create'], 'parent=' + MODx.request.id + '&context_key=' + MODx.ctx + '&class_key=' + Collections.template.children.resource_type + template);
    }

    ,createDerivativeChild: function(btn, e) {
        var template = '';
        if (Collections.template.children.template != null) {
            template = '&template=' + Collections.template.children.template;
        }

        MODx.loadPage(MODx.action['resource/create'], 'parent=' + MODx.request.id + '&context_key=' + MODx.ctx + '&class_key=' + btn.derivative + template);
    }

    ,viewChild: function(btn,e) {
        if (!this.menu.record.data) {
            window.open(this.menu.record.preview_url);
        } else {
            window.open(this.menu.record.data.preview_url);
        }

        return false;
    }

    ,duplicateChild: function(btn,e) {
        var r = {
            resource: this.menu.record.id
            ,is_folder: false
        };

        if (this.menu.record.data != undefined) {
            r.name = _('duplicate_of', {name: this.menu.record.data.pagetitle});
        } else {
            r.name = _('duplicate_of', {name: this.menu.record.pagetitle});
        }

        var w = MODx.load({
            xtype: 'modx-window-resource-duplicate'
            ,resource: this.menu.record.id
            ,hasChildren: false
            ,listeners: {
                'success': {fn:function() {this.refresh();},scope:this}
            }
        });
        w.config.hasChildren = false;
        w.setValues(r);
        w.show();
        return false;
    }

    ,deleteChild: function(btn,e) {
        MODx.msg.confirm({
            title: _('collections.children.delete')
            ,text: _('collections.children.delete_confirm')
            ,url: this.config.url
            ,params: {
                action: 'mgr/resource/delete'
                ,id: this.menu.record.id
            }
            ,listeners: {
                'success':{fn:this.refresh,scope:this}
            }
        });
    }

    ,removeChild: function(btn,e) {
        MODx.msg.confirm({
            title: _('collections.children.remove')
            ,text: _('collections.children.remove_confirm')
            ,url: this.config.url
            ,params: {
                action: 'mgr/resource/remove'
                ,id: this.menu.record.id
            }
            ,listeners: {
                'success':{fn:this.refresh,scope:this}
            }
        });
    }

    ,deleteSelected: function(btn,e) {
        var cs = this.getSelectedAsList();
        if (cs === false) return false;

        MODx.msg.confirm({
            title: _('collections.children.delete_multiple')
            ,text: _('collections.children.delete_multiple_confirm')
            ,url: this.config.url
            ,params: {
                action: 'mgr/resource/deletemultiple'
                ,ids: cs
            }
            ,listeners: {
                'success': {fn:function(r) {
                    this.getSelectionModel().clearSelections(true);
                    this.refresh();
                },scope:this}
            }
        });
        return true;
    }

    ,undeleteSelected: function(btn,e) {
        var cs = this.getSelectedAsList();
        if (cs === false) return false;

        MODx.Ajax.request({
            url: this.config.url
            ,params: {
                action: 'mgr/resource/undeletemultiple'
                ,ids: cs
            }
            ,listeners: {
                'success': {fn:function(r) {
                    this.getSelectionModel().clearSelections(true);
                    this.refresh();
                },scope:this}
            }
        });
        return true;
    }

    ,undeleteChild: function(btn,e) {
        MODx.Ajax.request({
            url: this.config.url
            ,params: {
                action: 'mgr/resource/undelete'
                ,id: this.menu.record.id
            }
            ,listeners: {
                'success':{fn:this.refresh,scope:this}
            }
        });
    }

    ,publishSelected: function(btn,e) {
        var cs = this.getSelectedAsList();
        if (cs === false) return false;

        MODx.Ajax.request({
            url: this.config.url
            ,params: {
                action: 'mgr/resource/publishmultiple'
                ,ids: cs
            }
            ,listeners: {
                'success': {fn:function(r) {
                    this.getSelectionModel().clearSelections(true);
                    this.refresh();
                },scope:this}
            }
        });
        return true;
    }

    ,unpublishSelected: function(btn,e) {
        var cs = this.getSelectedAsList();
        if (cs === false) return false;

        MODx.Ajax.request({
            url: this.config.url
            ,params: {
                action: 'mgr/resource/unpublishmultiple'
                ,ids: cs
            }
            ,listeners: {
                'success': {fn:function(r) {
                    this.getSelectionModel().clearSelections(true);
                    this.refresh();
                },scope:this}
            }
        });
        return true;
    }

    ,publishChild: function(btn,e) {
        MODx.Ajax.request({
            url: this.config.url
            ,params: {
                action: 'mgr/resource/publish'
                ,id: this.menu.record.id
            }
            ,listeners: {
                'success':{fn:this.refresh,scope:this}
            }
        });
    }

    ,unpublishChild: function(btn,e) {
        MODx.Ajax.request({
            url: this.config.url
            ,params: {
                action: 'mgr/resource/unpublish'
                ,id: this.menu.record.id
            }
            ,listeners: {
                'success':{fn:this.refresh,scope:this}
            }
        });
    }

    ,handleButtons: function(e){
        var t = e.getTarget();
        var elm = t.className.split(' ')[0];
        if(elm == 'controlBtn') {
            var action = t.className.split(' ')[1];
            var record = this.getSelectionModel().getSelected();
            this.menu.record = record;
            switch (action) {
                case 'delete':
                    this.deleteChild();
                    break;
                case 'undelete':
                    this.undeleteChild();
                    break;
                case 'edit':
                    this.editChild();
                    break;
                case 'duplicate':
                    this.duplicateChild();
                    break;
                case 'publish':
                    this.publishChild();
                    break;
                case 'unpublish':
                    this.unpublishChild();
                    break;
                case 'view':
                    this.viewChild();
                    break;
                case 'remove':
                    this.removeChild();
                    break;
                default:
                    window.location = record.data.edit_action;
                    break;
            }
        }
    }

    ,getDragDropText: function(){
        if (this.config.baseParams.sort != 'menuindex') {
            if (this.store.sortInfo == undefined || this.store.sortInfo.field != 'menuindex') {
                return _('collections.err.bad_sort_column', {column: 'menuindex'});
            }
        } else {
            if (this.store.sortInfo != undefined && this.store.sortInfo.field != 'menuindex') {
                return _('collections.err.bad_sort_column', {column: 'menuindex'});
            }
        }

        var search = Ext.getCmp('collections-child-search');
        var filter = Ext.getCmp('collections-grid-filter-status');
        if (search.getValue() != '' || filter.getValue() != '') {
            return _('collections.err.clear_filter');
        }

        return _('collections.global.change_order', {child: this.selModel.selections.items[0].data.pagetitle});
    }

    ,getDragDropTextOverTree: function(){
        return _('collections.global.change_parent', {child: this.selModel.selections.items[0].data.pagetitle});
    }

    ,registerGridDropTarget: function() {

        this.getView().dragZone = new Ext.grid.GridDragZone(this, {
            ddGroup : 'modx-treedrop-dd'
            ,originals: {}
            ,handleMouseDown: function(e) {
                // Disable drag and drop for clicking on checkbox (to select a row)
                if (e.target.className == 'x-grid3-row-checker') {
                    return false;
                }

                Ext.grid.GridDragZone.superclass.handleMouseDown.apply(this, arguments);
                return true;
            }
            ,onEndDrag: function() {
                var t = Ext.getCmp('modx-resource-tree');
                if (!t.dropZone) return false;

                t.dropZone.appendOnly = false;

                t.dropZone.onNodeDrop = this.originals.onNodeDrop;
                t.dropZone.onNodeOver = this.originals.onNodeOver;

                t.on('nodedragover', t._handleDrop, t);
                t.on('beforenodedrop', t._handleDrop, t);

                return true;
            }
            ,onInitDrag: function(e) {
                var data = this.dragData;
                this.ddel.innerHTML = this.grid.getDragDropText();
                this.proxy.update(this.ddel);

                var t = Ext.getCmp('modx-resource-tree');
                if (!t.dropZone) return false;

                t.dropZone.appendOnly = true;


                t.removeListener('nodedragover', t._handleDrop);
                t.removeListener('beforenodedrop', t._handleDrop);

                this.originals.onNodeDrop = t.dropZone.onNodeDrop;
                this.originals.onNodeOver = t.dropZone.onNodeOver;

                t.dropZone.onNodeDrop = function (nodeData, source, e) {
                    MODx.Ajax.request({
                        url: Collections.connectorUrl
                        ,params: {
                            action: 'mgr/resource/changeparent'
                            ,id: source.dragData.selections[0].id
                            ,parent: nodeData.node.attributes.id
                        }
                        ,listeners: {
                            'success': {
                                fn: function(r) {
                                    source.grid.refresh();
                                    return true;
                                },scope: this
                            }
                        }
                    });

                    return true;
                };

                t.dropZone.onNodeOver = function (nodeData, source,e, data) {
                    source.ddel.innerHTML = source.grid.getDragDropTextOverTree();
                    source.proxy.update(source.ddel);

                    return this.dropAllowed;
                };

                return true;
            }
        });
        this.getView().dragZone.addToGroup('modx-treedrop-dd');
        this.getView().dragZone.addToGroup('collectionChildDDGroup');

        var ddrow = new Ext.ux.dd.GridReorderDropTarget(this, {
            copy: false
            ,sortCol: 'menuindex'
            ,listeners: {
                'beforerowmove': function(objThis, oldIndex, newIndex, records) {
                }

                ,'afterrowmove': function(objThis, oldIndex, newIndex, records) {
                    MODx.Ajax.request({
                        url: Collections.connectorUrl
                        ,params: {
                            action: 'mgr/resource/ddreorder'
                            ,idItem: records.pop().id
                            ,oldIndex: oldIndex
                            ,newIndex: newIndex
                            ,parent: MODx.request.id
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
});
Ext.reg('collections-grid-children',Collections.grid.ContainerCollections);