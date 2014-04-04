MODx.on("ready",function() {
    var update = Ext.getCmp('modx-page-update-resource');
    if (!update) {
        return;
    }
    
    var parentId = update.config.record.parent;

    var cancelHandler = function(btn,e) {
        var fp = Ext.getCmp(this.config.formpanel);
        if (fp && fp.isDirty()) {
            Ext.Msg.confirm(_('warning'),_('resource_cancel_dirty_confirm'),function(e) {
                if (e == 'yes') {
                    MODx.releaseLock(MODx.request.id);
                    MODx.sleep(400);
                    MODx.loadPage(MODx.action['resource/update'], 'id='+parentId);
                }
            },this);
        } else {
            MODx.releaseLock(MODx.request.id);
            MODx.loadPage(MODx.action['resource/update'], 'id='+parentId);
        }
    };

    Ext.each(update.buttons, function(item, index) {
        if (item.text == _('cancel')) {
            item.handler = cancelHandler;

            return false;
        }
    });
});