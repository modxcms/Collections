Collections.page.CreateCategory = function(config) {
    config = config || {record:{}};
    config.record = config.record || {};
    Ext.applyIf(config,{
        panelXType: 'collections-panel-category'
    });
    config.canDuplicate = false;
    config.canDelete = false;
    Collections.page.CreateCategory.superclass.constructor.call(this,config);
};
Ext.extend(Collections.page.CreateCategory,MODx.page.CreateResource,{
    getButtons: function(cfg) {
        var btns = [];
        if (cfg.canSave == 1) {
            btns.push({
                process: 'create'
                ,id: 'modx-abtn-save'
                ,text: _('save')
                ,method: 'remote'
                ,checkDirty: false
                ,keys: [{
                    key: MODx.config.keymap_save || 's'
                    ,ctrl: true
                }]
            });
            btns.push('-');
        }
        btns.push({
            process: 'cancel'
            ,text: _('cancel')
            ,id: 'modx-abtn-cancel'
            ,params: { a: MODx.action['welcome'] }
        });
        return btns;
    }
});
Ext.reg('collections-page-category-create',Collections.page.CreateCategory);
