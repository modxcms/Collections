Ext.override(MODx.page.UpdateResource, {
    //cancel: function(btn,e) {
    //    var parentId = MODx.request.selection ? MODx.request.selection : this.config.record.parent;
    //
    //    var fp = Ext.getCmp(this.config.formpanel);
    //    if (fp && fp.isDirty()) {
    //        Ext.Msg.confirm(_('warning'),_('resource_cancel_dirty_confirm'),function(e) {
    //            if (e == 'yes') {
    //                fp.warnUnsavedChanges = false;
    //                MODx.releaseLock(MODx.request.id);
    //                MODx.sleep(400);
    //                MODx.loadPage('resource/update', 'id=' + parentId);
    //            }
    //        },this);
    //    } else {
    //        MODx.releaseLock(MODx.request.id);
    //        MODx.loadPage(MODx.action['resource/update'], 'id=' + parentId);
    //    }
    //}

    collectionsOriginals: {
        getButtons: MODx.page.UpdateResource.prototype.getButtons
    }
    ,getButtons: function(config) {
        var buttons = this.collectionsOriginals.getButtons.call(this, config);

        if (MODx.request.selection) {
            buttons.unshift({
                text: 'Back to Selection'
                ,handler: function() {
                    MODx.loadPage(MODx.action['resource/update'], 'id=' + MODx.request.selection);
                }
            });
        } else {
            buttons.unshift({
                text: 'Back to Collection'
                ,handler: function() {
                    MODx.loadPage(MODx.action['resource/update'], 'id=' + config.record.parent);
                }
            });
        }

        return buttons;
    }
});