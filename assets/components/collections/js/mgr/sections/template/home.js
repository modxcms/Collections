collections.page.TemplateHome = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        components: [{
            xtype: 'collections-panel-template-home'
            ,renderTo: 'collections-panel-template-home-div'
        }]
    });
    collections.page.TemplateHome.superclass.constructor.call(this,config);
};
Ext.extend(collections.page.TemplateHome,MODx.Component);
Ext.reg('collections-page-template-home',collections.page.TemplateHome);