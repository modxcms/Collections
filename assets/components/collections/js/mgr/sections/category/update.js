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

});
Ext.reg('collections-page-category-update',Collections.page.UpdateCategory);

Collections.page.UpdateSelection = function(config) {
    config = config || {record:{}};
    config.record = config.record || {};
    Ext.applyIf(config,{
        panelXType: 'collections-panel-selection'
    });
    config.canDelete = false;
    Collections.page.UpdateSelection.superclass.constructor.call(this,config);
};
Ext.extend(Collections.page.UpdateSelection,MODx.page.UpdateResource,{

});
Ext.reg('collections-page-selection-update',Collections.page.UpdateSelection);
