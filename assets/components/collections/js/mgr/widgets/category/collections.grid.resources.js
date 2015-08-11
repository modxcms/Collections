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
            ,parent: Collections.template.parent
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
        ,tbar: {
            xtype: 'container'
            ,layout: 'anchor'
            ,defaults: 
            {
                anchor : '100%' 
            }
            ,items: [
                new Ext.Toolbar({
                    items : this.getTbar(config)
                })
            ]
        }
    });
    Collections.grid.ContainerCollections.superclass.constructor.call(this,config);
    this.on('rowclick',MODx.fireResourceFormChange);
    this.on('click', this.handleButtons, this);
    
    this.currentFolder = null;

    this.bc = new Ext.Toolbar({
        id: 'c-bc'
        ,hidden: true
        ,crumbs: [{
            id: 2
            ,text: 'Collections'
        }]
        ,data : [{
            id: 2
            ,text: 'Collections'
        }],
        grid: this,
        tpl: new Ext.XTemplate('<div class="crumb_wrapper collections_crumb_wrapper">' +
            '<ul class="crumbs">' +
            '<tpl for=".">' +
                '<tpl if="xindex!==xcount">' +
                    '<tpl if="xindex==1">' +
                        '<li class="first"><button type="button" data-id="{id}" class="root">{text}</button></li>' +
                    '</tpl>' +
                    '<tpl if="xindex!==1">' +
                        '<li><button class="text" data-id="{id}">{text}</button></li>' +
                    '</tpl>' +
                '</tpl>' +
                '<tpl if="xindex==xcount">' +
                    '<tpl if="xindex==1">' +
                        '<li class="first"><span data-id="{id}" class="root">{text}</span></li>' +
                    '</tpl>' +
                    '<tpl if="xindex!==1">' +
                        '<li><span class="text" data-id="{id}">{text}</span></li>' +
                    '</tpl>' +
                '</tpl>' +
            '</tpl>' +
            '</ul></div>', {
            compiled: true
        }),
        listeners: {
            render: {
                fn: function(toolbar){
                    toolbar.el.on('click', function (e) {
                        if(e.target.nodeName.toLowerCase() != 'button') return;
                        
                        var newCrumbs = [];
                        for (var i = 0; i < toolbar.crumbs.length; i++) {
                            newCrumbs.push(toolbar.crumbs[i]);
                            if (toolbar.crumbs[i]['id'] == e.target.dataset.id) {
                                break;
                            }
                        }
                        
                        toolbar.crumbs = newCrumbs;
                        toolbar.tpl.overwrite(toolbar.el, toolbar.crumbs);
                        if (toolbar.crumbs.length == 1) {
                            toolbar.hide();
                        }
                        
                        toolbar.grid.currentFolder = e.target.dataset.id;
                        
                        toolbar.grid.store.removeAll();

                        toolbar.grid.getStore().baseParams.parent = e.target.dataset.id;
                        toolbar.grid.getBottomToolbar().changePage(1);

                        toolbar.grid.body.slideIn('r', {stopFx:true, duration:.2});
                    });
                },
                scope: this
            }
        }
    });
    this.getTopToolbar().add(this.bc);
    
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
    
    ,openChild: function(){
        this.store.removeAll();
        
        this.getStore().baseParams.parent = this.menu.record.id;
        this.getBottomToolbar().changePage(1);
        
        this.bc.crumbs.push({
            id: this.menu.record.id,
            text: this.menu.record.pagetitle
        });

        this.currentFolder = this.menu.record.id;
        
        this.bc.tpl.overwrite(this.bc.el, this.bc.crumbs);
        this.bc.show();
        
        this.body.slideIn('r', {stopFx:true, duration:.2});       
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
            ,'parent': Collections.template.parent
            ,sort: Collections.template.sort.field
            ,dir: Collections.template.sort.dir
        };
        Ext.getCmp('collections-child-search').reset();
        Ext.getCmp('collections-grid-filter-status').reset();
        this.getBottomToolbar().changePage(1);
    }

    ,editChild: function(btn,e) {
        var selection = '';
        if (Collections.template.parent != MODx.request.id){
           selection = '&selection=' + MODx.request.id;
        }

        var collectionGet = '';
        if (this.currentFolder) {
            collectionGet = '&collection=' + Collections.template.parent
        }
        
        MODx.loadPage(MODx.request.a, 'id=' + this.menu.record.id + selection + collectionGet);
    }

    ,createChild: function(btn,e) {
        var template = '';
        var selection = '';
        if (Collections.template.children.template != null) {
            template = '&template=' + Collections.template.children.template;
        }
        if (Collections.template.parent != MODx.request.id){
           selection = '&selection=' + MODx.request.id;
        }

        var collectionGet = '';
        if (this.currentFolder) {
            collectionGet = '&collection=' + Collections.template.parent
        }
        
        MODx.loadPage(MODx.action['resource/create'], 'parent=' + (this.currentFolder || Collections.template.parent) + collectionGet + '&context_key=' + Collections.template.parent_context + '&class_key=' + Collections.template.children.resource_type + template + selection);
    }

    ,createDerivativeChild: function(btn, e) {
        var template = '';
        var selection = '';
        if (Collections.template.children.template != null) {
            template = '&template=' + Collections.template.children.template;
        }
        if (Collections.template.parent != MODx.request.id){
           selection = '&selection=' + MODx.request.id;
        }

        var collectionGet = '';
        if (this.currentFolder) {
            collectionGet = '&collection=' + Collections.template.parent
        }

        MODx.loadPage(MODx.action['resource/create'], 'parent=' + (this.currentFolder || Collections.template.parent) + collectionGet + '&context_key=' + Collections.template.parent_context + '&class_key=' + btn.derivative + template + selection);
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
        var elm;
        var action = null;
        if (t.dataset.action) {
            action = t.dataset.action;
        } else {
            elm = t.className.split(' ')[0];
            if(elm == 'controlBtn') {
                action = t.className.split(' ')[1];
                
            }
        }
        
        if(action) {
            var record = this.getSelectionModel().getSelected();
            if (!record && t.dataset.id) {
                record = this.store.getById(t.dataset.id);
            }
            
            if (!record) {
                return;
            }

            if (record.data) {
                record = record.data;
            }
            
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
                case 'open':
                    this.openChild();
                    break;
                default:
                    this.editChild();
                    break;
            }
        }
    }
    
    ,parseSortField: function(sort){
        return sort.split(':')[0];             
    }

    ,getDragDropText: function(){
        if (this.parseSortField(this.config.baseParams.sort) != 'menuindex') {
            if (this.store.sortInfo == undefined || this.parseSortField(this.store.sortInfo.field) != 'menuindex') {
                return _('collections.err.bad_sort_column', {column: 'menuindex'});
            }
        } else {
            if (this.store.sortInfo != undefined && this.parseSortField(this.store.sortInfo.field) != 'menuindex') {
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
                            ,parent: Collections.template.parent
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