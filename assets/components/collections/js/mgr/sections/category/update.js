collections.page.UpdateCategory = function(config) {
    config = config || {record:{}};
    config.record = config.record || {};
    Ext.applyIf(config,{
        panelXType: 'collections-panel-category'
    });
    config.canDelete = false;
    collections.page.UpdateCategory.superclass.constructor.call(this,config);
};
Ext.extend(collections.page.UpdateCategory,MODx.page.UpdateResource,{

});
Ext.reg('collections-page-category-update',collections.page.UpdateCategory);

collections.page.UpdateSelection = function(config) {
    config = config || {record:{}};
    config.record = config.record || {};
    Ext.applyIf(config,{
        panelXType: 'collections-panel-selection'
    });
    config.canDelete = false;
    collections.page.UpdateSelection.superclass.constructor.call(this,config);
};
Ext.extend(collections.page.UpdateSelection,MODx.page.UpdateResource,{

});
Ext.reg('collections-page-selection-update',collections.page.UpdateSelection);
