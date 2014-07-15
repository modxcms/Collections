Ext.onReady(function() {
    MODx.load({ xtype: 'collections-page-template-home'});
});

Collections.page.TemplateHome = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        components: [{
            xtype: 'collections-panel-template-home'
            ,renderTo: 'collections-panel-template-home-div'
        }]
    });
    Collections.page.TemplateHome.superclass.constructor.call(this,config);
};
Ext.extend(Collections.page.TemplateHome,MODx.Component);
Ext.reg('collections-page-template-home',Collections.page.TemplateHome);