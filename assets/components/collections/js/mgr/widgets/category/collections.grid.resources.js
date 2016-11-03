collections.grid.ContainerCollections = function(config) {
    config = config || {};
    this.sm = new Ext.grid.CheckboxSelectionModel();
    Ext.applyIf(config,{
        id: 'collections-grid-container-collections'
        ,title: _('collections.collections')
        ,url: collections.connectorUrl
        ,autosave: true
        ,save_action: 'mgr/resource/updatefromgrid'
        ,ddGroup: 'collectionChildDDGroup'
        ,enableDragDrop: false
        ,baseParams: {
            action: 'mgr/resource/getlist'
            ,parent: collections.template.parent
            ,sort: collections.template.sort.field
            ,dir: collections.template.sort.dir
        }
        ,fields: collections.template.fields
        ,paging: true
        ,remoteSort: true
        ,pageSize: collections.template.pageSize
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
    collections.grid.ContainerCollections.superclass.constructor.call(this,config);
    this.on('rowclick',MODx.fireResourceFormChange);
    this.on('click', this.handleButtons, this);

    window.history.replaceState({}, '', window.location.href);
    
    this.initBreadCrumbs(config);
    
    if (collections.template.allowDD) {
        this.on('render', this.registerGridDropTarget, this);
        this.on('beforedestroy', this.destroyScrollManager, this);
    }
};
Ext.extend(collections.grid.ContainerCollections,MODx.grid.Grid,{
    currentFolder: null
    
    ,getMenu: function() {
        var m = [];
        if (!this.menu.record) return m;

        var addDelimiter = false;
        Ext.each(collections.template.context_menu, function(key) {
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
        
        this.pushHistoryState(this.menu.record.id);
    }

    ,getColumns: function(config) {
        var columns = collections.template.columns;

        if (collections.template.bulkActions) {
            columns.unshift(this.sm);
        }

        return columns;
    }

    ,getTbar: function(config) {
        var items = [];

        if (collections.template.resource_type_selection && collections.template.resourceDerivatives.length > 0) {
            var resourceDerivatives = [];

            Ext.each(collections.template.resourceDerivatives, function(item){
                resourceDerivatives.push({
                    text: item.name
                    ,derivative: item.id
                    ,handler: this.createDerivativeChild
                    ,scope: this
                });
            }, this);

            items.push({
                text: (_(collections.template.button_label) == undefined) ? collections.template.button_label : _(collections.template.button_label)
                ,handler: this.createChild
                ,xtype: 'splitbutton'
                ,scope: this
                ,menu: resourceDerivatives
            });
        } else {
            items.push({
                text: (_(collections.template.button_label) == undefined) ? collections.template.button_label : _(collections.template.button_label)
                ,handler: this.createChild
                ,scope: this
            });
        }


        if (collections.template.bulkActions) {
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
            ,'parent': collections.template.parent
            ,sort: collections.template.sort.field
            ,dir: collections.template.sort.dir
        };
        Ext.getCmp('collections-child-search').reset();
        Ext.getCmp('collections-grid-filter-status').reset();
        this.getBottomToolbar().changePage(1);
    }

    ,editChild: function(btn,e) {
        var selection = '';
        if (collections.template.parent != MODx.request.id){
           selection = '&selection=' + MODx.request.id;
        }

        var collectionGet = '';
        if (this.currentFolder) {
            collectionGet = '&collection=' + collections.template.parent
        }
        
        var folderGet = '';
        var query = Ext.urlDecode(location.search.replace('?', ''));
        if (parseInt(query.folder) > 0) {
            folderGet = '&folder=' + parseInt(query.folder); 
        }
        
        MODx.loadPage(MODx.request.a, 'id=' + this.menu.record.id + selection + collectionGet + folderGet);
    }

    ,createChild: function(btn,e) {
        var template = '';
        var selection = '';
        if (collections.template.children.template != null) {
            template = '&template=' + collections.template.children.template;
        }
        if (collections.template.parent != MODx.request.id){
           selection = '&selection=' + MODx.request.id;
        }

        var collectionGet = '';
        if (this.currentFolder) {
            collectionGet = '&collection=' + collections.template.parent
        }

        var folderGet = '';
        var query = Ext.urlDecode(location.search.replace('?', ''));
        if (parseInt(query.folder) > 0) {
            folderGet = '&folder=' + parseInt(query.folder);
        }
        
        MODx.loadPage(MODx.action['resource/create'], 'parent=' + (this.currentFolder || collections.template.parent) + collectionGet + '&context_key=' + collections.template.parent_context + '&class_key=' + collections.template.children.resource_type + template + selection + folderGet);
    }

    ,createDerivativeChild: function(btn, e) {
        var template = '';
        var selection = '';
        if (collections.template.children.template != null) {
            template = '&template=' + collections.template.children.template;
        }
        if (collections.template.parent != MODx.request.id){
           selection = '&selection=' + MODx.request.id;
        }

        var collectionGet = '';
        if (this.currentFolder) {
            collectionGet = '&collection=' + collections.template.parent
        }

        var folderGet = '';
        var query = Ext.urlDecode(location.search.replace('?', ''));
        if (parseInt(query.folder) > 0) {
            folderGet = '&folder=' + parseInt(query.folder);
        }

        MODx.loadPage(MODx.action['resource/create'], 'parent=' + (this.currentFolder || collections.template.parent) + collectionGet + '&context_key=' + collections.template.parent_context + '&class_key=' + btn.derivative + template + selection + folderGet);
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
            if (t.dataset.id) {
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
                case 'quickupdate':
                    this.quickupdateChild();
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
        
        if (this.parsePermanentSort('menuindex')) {
            return _('collections.err.permanent_sort', {column: 'menuindex'});
        }

        var search = Ext.getCmp('collections-child-search');
        var filter = Ext.getCmp('collections-grid-filter-status');
        if (search.getValue() != '' || filter.getValue() != '') {
            return _('collections.err.clear_filter');
        }

        return _('collections.global.change_order', {child: this.selModel.selections.items[0].data.pagetitle});
    }

    ,parsePermanentSort: function(column){
        if (collections.template.permanent_sort.before.indexOf('*') != -1) return true;
        if (collections.template.permanent_sort.after.indexOf('*') != -1) return true;
        
        var found = false;
        
        Ext.each(collections.template.permanent_sort.before.split(',').concat(collections.template.permanent_sort.after.split(',')).filter(function(e){return e}), function(item){
            if (item.indexOf('=') == -1) {
                found = true;
                return false;
            }                
            
            if (item.replace(/ /g, '').indexOf('menuindex=') != -1) {
                found = true;
                return false;
            }
        });      
        
        return found;
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
                        url: collections.connectorUrl
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
                    var parent = collections.template.parent;
                    
                    var query = Ext.urlDecode(location.search.replace('?', ''));
                    if (parseInt(query.folder) > 0) {
                        parent = parseInt(query.folder);
                    }
                    
                    MODx.Ajax.request({
                        url: collections.connectorUrl
                        ,params: {
                            action: 'mgr/resource/ddreorder'
                            ,idItem: records.pop().id
                            ,oldIndex: oldIndex
                            ,newIndex: newIndex
                            ,parent: parent 
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

    ,quickupdateChild: function(btn, e) {
        MODx.Ajax.request({
            url: MODx.config.connector_url
            ,params: {
                action: 'resource/get'
                ,id: this.menu.record.id
            }
            ,listeners: {
                'success': {fn:function(r) {
                    var pr = r.object;

                    var w = MODx.load({
                        xtype: 'modx-window-quick-update-modResource'
                        ,record: pr
                        ,listeners: {
                            'success':{fn:function(r) {
                                this.refresh();
                                var newTitle = '<span dir="ltr">' + r.f.findField('pagetitle').getValue() + ' (' + w.record.id + ')</span>';
                                w.setTitle(w.title.replace(/<span.*\/span>/, newTitle));
                            },scope:this}
                            ,'hide':{fn:function() {this.destroy();}}
                        }
                    });
                    w.title += ': <span dir="ltr">' + w.record.pagetitle + ' ('+ w.record.id + ')</span>';
                    w.setValues(r.object);
                    w.show();
                },scope:this}
            }
        });
    }
    
    ,_loadStore: function() {
        if (this.config.grouping) {
            this.store = new Ext.data.GroupingStore({
                url: this.config.url
                ,baseParams: this.config.baseParams || { action: this.config.action || 'getList'}
                ,reader: new Ext.data.JsonReader({
                    totalProperty: 'total'
                    ,root: 'results'
                    ,fields: this.config.fields
                })
                ,sortInfo:{
                    field: this.config.sortBy || 'id'
                    ,direction: this.config.sortDir || 'ASC'
                }
                ,remoteSort: this.config.remoteSort || false
                ,groupField: this.config.groupBy || 'name'
                ,storeId: this.config.storeId || Ext.id()
                ,autoDestroy: true
                ,listeners: {
                    beforeload: {
                        fn: this.loadBreadCrumbs,
                        scope: this,
                        single: true
                    },
                    load: function(){
                        Ext.getCmp('modx-content').doLayout(); /* Fix layout bug with absolute positioning */
                    }
                }
            });
        } else {
            this.store = new Ext.data.JsonStore({
                url: this.config.url
                ,baseParams: this.config.baseParams || { action: this.config.action || 'getList' }
                ,fields: this.config.fields
                ,root: 'results'
                ,totalProperty: 'total'
                ,remoteSort: this.config.remoteSort || false
                ,storeId: this.config.storeId || Ext.id()
                ,autoDestroy: true
                ,listeners: {
                    beforeload: {
                        fn: this.loadBreadCrumbs,
                        scope: this,
                        single: true
                    },
                    load: function(){
                        Ext.getCmp('modx-content').doLayout(); /* Fix layout bug with absolute positioning */
                    }
                }
            });
        }
    }
    
    ,loadBreadCrumbs: function(store, options){
        var folder = parseInt(MODx.request.folder);
        
        if (folder > 0) {
            this.currentFolder = folder;
            options.params.parent = folder;
            store.baseParams.parent = folder;       
        }
    }
    
    ,pushHistoryState: function(id){
        try {
            var query = Ext.urlDecode(location.search.replace('?', ''));
            var loc = location.href;
    
            if (query.folder) {
                if (id) {
                    loc = loc.replace('folder=' + query.folder, 'folder=' + id);
                } else {
                    loc = loc.replace('&folder=' + query.folder, '');
                }
            } else {
                if (id) {
                    loc += '&folder=' + id;
                }
            }
    
            window.history.pushState({}, '', loc);
        } catch (err) {}
    }
    
    ,initBreadCrumbs: function(config){
        this.addEvents('breadCrumbsBeforeRender');
        this.addEvents('breadCrumbsRender');

        window.addEventListener('popstate', function(event) {
            if (event.state) {
                location.reload();
            }
        }, false);
        
        this.on('breadCrumbsRender', function(toolbar){
            var folder = parseInt(MODx.request.folder);
            var collection = parseInt(MODx.request.id);

            if ((folder > 0) && (collection > 0)) {
                MODx.Ajax.request({
                    url: collections.connectorUrl
                    ,params: {
                        action: 'mgr/extra/breadcrumbs'
                        ,collection: collection
                        ,folder: folder
                    }
                    ,listeners: {
                        success: {
                            fn: function(r) {
                                Ext.each(r.results, function(item){
                                    toolbar.crumbs.push({
                                        id: item.id,
                                        text: item.text
                                    });
                                });

                                toolbar.tpl.overwrite(toolbar.el, toolbar.crumbs);
                                toolbar.show();
                            },scope: this
                        },
                        failure: {
                            fn: function(){
                                this.store.removeAll();
                                this.currentFolder = collections.template.parent;
                                this.getStore().baseParams.parent = collections.template.parent;
                                this.getBottomToolbar().changePage(1);
                            },
                            scope: this
                        }
                    }
                });
            }
        }, this);

        this.bc = new Ext.Toolbar({
            hidden: true
            ,crumbs: [{
                id: collections.template.parent
                ,text: config.resourcePanel.record.pagetitle
            }]
            ,data : [{
                id: collections.template.parent
                ,text: config.resourcePanel.record.pagetitle
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
                                this.pushHistoryState();
                            } else {
                                this.pushHistoryState(e.target.dataset.id);
                            }


                            toolbar.grid.currentFolder = e.target.dataset.id;

                            toolbar.grid.store.removeAll();

                            toolbar.grid.getStore().baseParams.parent = e.target.dataset.id;
                            toolbar.grid.getBottomToolbar().changePage(1);

                            toolbar.grid.body.slideIn('r', {stopFx:true, duration:.2});
                        }, this);

                        this.fireEvent('breadCrumbsRender', toolbar);
                    },
                    scope: this
                },
                beforerender: {
                    fn: function(toolbar){
                        this.fireEvent('breadCrumbsBeforeRender', toolbar);
                    },
                    scope: this
                }
            }
        });
        this.getTopToolbar().add(this.bc);                 
    }
});
Ext.reg('collections-grid-children',collections.grid.ContainerCollections);