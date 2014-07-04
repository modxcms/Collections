Collections.page.UpdateCategory = function(config) {
    config = config || {record:{}};
    config.record = config.record || {};
    Ext.applyIf(config,{
        panelXType: 'collections-panel-category'
    });
    config.canDelete = false;
    Collections.page.UpdateCategory.superclass.constructor.call(this,config);
};
Ext.extend(Collections.page.UpdateCategory,MODx.page.UpdateResource,{
    getButtons: function(cfg) {
        var btns = [];
        if (cfg.canSave == 1) {
            btns.push({
                process: 'resource/update'
                ,text: _('save')
                ,method: 'remote'
                ,checkDirty: false
                ,id: 'modx-abtn-save'
                ,keys: [{
                    key: MODx.config.keymap_save || 's'
                    ,ctrl: true
                }]
            });
            btns.push('-');
        } else if (cfg.locked) {
            btns.push({
                text: cfg.lockedText || _('locked')
                ,handler: Ext.emptyFn
                ,id: 'modx-abtn-locked'
                ,disabled: true
            });
            btns.push('-');
        }
        btns.push({
            process: 'resource/preview'
            ,text: _('view')
            ,handler: this.preview
            ,scope: this
            ,id: 'modx-abtn-preview'
        });
        btns.push('-');
        btns.push({
            process: 'cancel'
            ,text: _('cancel')
            ,handler: this.cancel
            ,scope: this
            ,id: 'modx-abtn-cancel'
        });
        return btns;
    }
});
Ext.reg('collections-page-category-update',Collections.page.UpdateCategory);
