Ext.ux.dd.GridReorderDropTarget = function(grid, config) {
    this.target = new Ext.dd.DropTarget(grid.getEl(), {
        ddGroup: grid.ddGroup || 'GridDD'
        ,grid: grid
        ,sortCol: 'menuindex'
        ,gridDropTarget: this
        ,notifyDrop: function(dd, e, data){
            if (!data.grid.parseSortField) {
                data.grid.parseSortField = function (value) {return value;}
            }
            
            if (!data.grid.parsePermanentSort) {
                data.grid.parsePermanentSort = function (value) {return false;}
            }
            
            if (data.grid.parseSortField(data.grid.config.baseParams.sort) != this.sortCol) {
                if (data.grid.store.sortInfo == undefined || data.grid.parseSortField(data.grid.store.sortInfo.field) != this.sortCol) {
                    return false;
                }
            } else {
                if (data.grid.store.sortInfo != undefined && data.grid.parseSortField(data.grid.store.sortInfo.field) != this.sortCol) {
                    return false;
                }
            }

            if (data.grid.parsePermanentSort(this.sortCol)) {
                return false;
            }

            var search = Ext.getCmp('collections-child-search');
            var filter = Ext.getCmp('collections-grid-filter-status');
            if (search != undefined && filter != undefined) {
                if (search.getValue() != '' || filter.getValue() != '') {
                    return false;
                }
            }

            // determine the row
            var t = Ext.lib.Event.getTarget(e);
            var rindex = this.grid.getView().findRowIndex(t);
            if (rindex === false) return false;
            if (rindex == data.rowIndex) return false;

            var menuIndexes = {};
            menuIndexes.oldIndex = this.grid.store.data.items[data.rowIndex].data[this.sortCol];
            menuIndexes.newIndex = this.grid.store.data.items[rindex].data[this.sortCol];

            // fire the before move/copy event
            if (this.gridDropTarget.fireEvent(this.copy?'beforerowcopy':'beforerowmove', this.gridDropTarget, menuIndexes.oldIndex, menuIndexes.newIndex, data.selections) === false) return false;

            // update the store
            var ds = this.grid.getStore();
            if (!this.copy) {
                for(i = 0; i < data.selections.length; i++) {
                    ds.remove(ds.getById(data.selections[i].id));
                }
            }
            ds.insert(rindex,data.selections);

            // re-select the row(s)
            var sm = this.grid.getSelectionModel();
            if (sm) sm.selectRecords(data.selections);

            // fire the after move/copy event
            this.gridDropTarget.fireEvent(this.copy?'afterrowcopy':'afterrowmove', this.gridDropTarget, menuIndexes.oldIndex, menuIndexes.newIndex, data.selections);

            return true;
        }
        ,notifyOver: function(dd, e, data) {
            this.grid.getView().dragZone.ddel.innerHTML = this.grid.getDragDropText();
            this.grid.getView().dragZone.proxy.update(this.grid.getView().dragZone.ddel);

            if (!data.grid.parseSortField) {
                data.grid.parseSortField = function (value) {return value;}
            }
            
            if (!data.grid.parsePermanentSort) {
                data.grid.parsePermanentSort = function (value) {return false;}
            }
            
            if (data.grid.parseSortField(data.grid.config.baseParams.sort) != this.sortCol) {
                if (data.grid.store.sortInfo == undefined || data.grid.parseSortField(data.grid.store.sortInfo.field) != this.sortCol) {
                    return this.dropNotAllowed;
                }
            } else {
                if (data.grid.store.sortInfo != undefined && data.grid.parseSortField(data.grid.store.sortInfo.field) != this.sortCol) {
                    return this.dropNotAllowed;
                }
            }

            if (data.grid.parsePermanentSort(this.sortCol)) {
                return false;
            }

            var search = Ext.getCmp('collections-child-search');
            var filter = Ext.getCmp('collections-grid-filter-status');
            if (search != undefined && filter != undefined) {
                if (search.getValue() != '' || filter.getValue() != '') {
                    return this.dropNotAllowed;
                }
            }

            var t = Ext.lib.Event.getTarget(e);
            var rindex = this.grid.getView().findRowIndex(t);
            if (rindex == data.rowIndex) rindex = false;

            return (rindex === false)? this.dropNotAllowed : this.dropAllowed;
        }
    });
    if (config) {
        Ext.apply(this.target, config);
        if (config.listeners) Ext.apply(this,{listeners: config.listeners});
    }

    this.addEvents({
        "beforerowmove": true
        ,"afterrowmove": true
        ,"beforerowcopy": true
        ,"afterrowcopy": true
    });

    Ext.ux.dd.GridReorderDropTarget.superclass.constructor.call(this);
};

Ext.extend(Ext.ux.dd.GridReorderDropTarget, Ext.util.Observable, {
    getTarget: function() {
        return this.target;
    }
    ,getGrid: function() {
        return this.target.grid;
    }
    ,getCopy: function() {
        return this.target.copy?true:false;
    }
    ,setCopy: function(b) {
        this.target.copy = b?true:false;
    }
});