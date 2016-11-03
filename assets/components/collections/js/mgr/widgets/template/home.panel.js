collections.panel.TemplateHome = function(config) {
    config = config || {};
    Ext.apply(config,{
        border: false
        ,baseCls: 'modx-formpanel'
        ,cls: 'container'
        ,items: [{
            html: '<h2>'+_('collections.template.page_title')+'</h2>'
            ,border: false
            ,cls: 'modx-page-header'
        },{
            xtype: 'modx-tabs'
            ,defaults: { border: false ,autoHeight: true }
            ,border: true
            ,activeItem: 0
            ,hideMode: 'offsets'
            ,items: [{
                title: _('collections.template.templates')
                ,items: [{
                    html: '<p>'+_('collections.template.templates_desc')+'</p>'
                    ,border: false
                    ,bodyCssClass: 'panel-desc'
                },{
                    xtype: 'collections-grid-template'
                    ,preventRender: true
                    ,cls: 'main-wrapper'
                }]
            }]
        }]
    });
    collections.panel.TemplateHome.superclass.constructor.call(this,config);
};
Ext.extend(collections.panel.TemplateHome,MODx.Panel);
Ext.reg('collections-panel-template-home',collections.panel.TemplateHome);