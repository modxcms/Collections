ContainerX.combo.FilterStatus = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        store: [
            ['',_('containerx.system.all')]
            ,['published',_('published')]
            ,['unpublished',_('unpublished')]
            ,['deleted',_('deleted')]
        ]
        ,name: 'filter'
        ,hiddenName: 'filter'
        ,triggerAction: 'all'
        ,editable: false
        ,selectOnFocus: false
        ,preventRender: true
        ,forceSelection: true
        ,enableKeyEvents: true
    });
    ContainerX.combo.FilterStatus.superclass.constructor.call(this,config);
};
Ext.extend(ContainerX.combo.FilterStatus,MODx.combo.ComboBox);
Ext.reg('containerx-combo-filter-status',ContainerX.combo.FilterStatus);