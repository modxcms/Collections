ContainerX.combo.LocationState = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        store: new Ext.data.SimpleStore({
            fields: ['d','v']
            ,data: [
                [_('containerx.location.state_new'), 'new']
                ,[_('containerx.location.state_published'), 'published']
                ,[_('containerx.location.state_unpublished'), 'unpublished']
            ]
        })
        ,displayField: 'd'
        ,valueField: 'v'
        ,value: 'new'
        ,mode: 'local'
        ,triggerAction: 'all'
        ,editable: false
        ,selectOnFocus: false
        ,preventRender: true
        ,forceSelection: true
        ,enableKeyEvents: true
    });
    ContainerX.combo.LocationState.superclass.constructor.call(this,config);
};
Ext.extend(ContainerX.combo.LocationState,MODx.combo.ComboBox);
Ext.reg('containerx-combo-location-state',ContainerX.combo.LocationState);

ContainerX.combo.LocationCategory = function(config, getStore) {
    config = config || {};
    Ext.applyIf(config,{
        name: 'category'
        ,hiddenName: 'category'
        ,displayField: 'pagetitle'
        ,valueField: 'id'
        ,fields: ['pagetitle','id']
        ,mode: 'remote'
        ,triggerAction: 'all'
        ,typeAhead: true
        ,editable: true
        ,forceSelection: false
        ,pageSize: 20
        ,stackItems: true
        ,url: ContainerX.config.connectorUrl
        ,baseParams: {action: 'mgr/category/getlist'}
    });
    Ext.applyIf(config,{
        store: new Ext.data.JsonStore({
            url: config.url
            ,root: 'results'
            ,totalProperty: 'total'
            ,fields: config.fields
            ,errorReader: MODx.util.JSONReader
            ,baseParams: config.baseParams || {}
            ,remoteSort: config.remoteSort || false
            ,autoDestroy: true
        })
    });
    if (getStore === true) {
        config.store.load();
        return config.store;
    }
    ContainerX.combo.LocationCategory.superclass.constructor.call(this,config);
    this.config = config;
    return this;
};
Ext.extend(ContainerX.combo.LocationCategory,Ext.ux.form.SuperBoxSelect);
Ext.reg('containerx-combo-location-category',ContainerX.combo.LocationCategory);