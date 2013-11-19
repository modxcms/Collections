Collections.combo.FilterStatus = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        store: [
            ['',_('collections.system.all')]
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
    Collections.combo.FilterStatus.superclass.constructor.call(this,config);
};
Ext.extend(Collections.combo.FilterStatus,MODx.combo.ComboBox);
Ext.reg('collections-combo-filter-status',Collections.combo.FilterStatus);