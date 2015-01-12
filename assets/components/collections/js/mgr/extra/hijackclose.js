Ext.override(Collections_new_child ? MODx.page.CreateResource : MODx.page.UpdateResource, {
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
        getButtons: Collections_new_child ? MODx.page.CreateResource.prototype.getButtons : MODx.page.UpdateResource.prototype.getButtons
    }
    ,getButtons: function(config) {
        var buttons = this.collectionsOriginals.getButtons.call(this, config);

        if (MODx.request.selection) {
            buttons.unshift({
                text: _(Collections_labels.back_to_selection) ? _(Collections_labels.back_to_selection) : Collections_labels.back_to_selection
                ,handler: function() {
                    MODx.loadPage(MODx.action['resource/update'], 'id=' + MODx.request.selection);
                }
            });
        } else {
            buttons.unshift({
                text: _(Collections_labels.back_to_collection) ? _(Collections_labels.back_to_collection) : Collections_labels.back_to_collection
                ,handler: function() {
                    MODx.loadPage(MODx.action['resource/update'], 'id=' + config.record.parent);
                }
            });
        }

        return buttons;
    }
});