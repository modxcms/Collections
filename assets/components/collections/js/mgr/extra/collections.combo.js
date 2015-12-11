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

Collections.combo.Template = function(config, getStore) {
    config = config || {};

    if (!config.clearBtnCls) {
        if (MODx.config.connector_url) {
            config.clearBtnCls = 'x-form-trigger';
        } else {
            config.clearBtnCls = null;
        }
    }

    if (!config.expandBtnCls) {
        if (MODx.config.connector_url) {
            config.expandBtnCls = 'x-form-trigger';
        } else {
            config.expandBtnCls = null;
        }
    }

    Ext.applyIf(config,{
        name: 'templates'
        ,hiddenName: 'templates[]'
        ,displayField: 'templatename'
        ,valueField: 'id'
        ,fields: ['templatename','id']
        ,mode: 'remote'
        ,triggerAction: 'all'
        ,typeAhead: true
        ,editable: true
        ,forceSelection: false
        ,pageSize: 20
        ,url: Collections.config.connectorUrl
        ,baseParams: {
            action: 'mgr/extra/gettemplates'
            ,template: (MODx.request.id != undefined) ? MODx.request.id : 0
            ,addEmpty: 1
        }
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
    Collections.combo.Template.superclass.constructor.call(this,config);
    this.config = config;
    return this;
};
Ext.extend(Collections.combo.Template,Ext.ux.form.SuperBoxSelect);
Ext.reg('collections-combo-template',Collections.combo.Template);

Collections.combo.SortDir = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        store: [
            ['asc',_('sort_asc')]
            ,['desc',_('sort_desc')]
        ]
        ,name: 'sortdir'
        ,hiddenName: 'sortdir'
        ,editable: false
        ,selectOnFocus: false
        ,preventRender: true
        ,forceSelection: true
        ,enableKeyEvents: true
    });
    Collections.combo.SortDir.superclass.constructor.call(this,config);
};
Ext.extend(Collections.combo.SortDir,MODx.combo.ComboBox);
Ext.reg('collections-combo-sort-dir',Collections.combo.SortDir);

Collections.combo.CollectionsTemplate = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        name: 'collections_template'
        ,hiddenName: 'collections_template'
        ,displayField: 'name'
        ,valueField: 'id'
        ,fields: ['name','id']
        ,pageSize: 20
        ,url: Collections.config.connectorUrl
        ,baseParams:{
            action: 'mgr/template/getlist'
        }
    });
    Collections.combo.CollectionsTemplate.superclass.constructor.call(this,config);
};
Ext.extend(Collections.combo.CollectionsTemplate,MODx.combo.ComboBox);
Ext.reg('collections-combo-collections-template',Collections.combo.CollectionsTemplate);

Collections.combo.SingleTemplate = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        name: 'resource_template'
        ,hiddenName: 'resource_template'
        ,displayField: 'templatename'
        ,valueField: 'id'
        ,fields: ['templatename','id']
        ,pageSize: 20
        ,url: Collections.config.connectorUrl
        ,baseParams:{
            action: 'mgr/extra/gettemplates'
            ,addEmpty: config.addEmpty | 0
        }
    });
    Collections.combo.SingleTemplate.superclass.constructor.call(this,config);
};
Ext.extend(Collections.combo.SingleTemplate,MODx.combo.ComboBox);
Ext.reg('collections-combo-single-template',Collections.combo.SingleTemplate);

Collections.combo.ContentPlace = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        store: new Ext.data.SimpleStore({
            fields: ['d','v']
            ,data: [
                [_('collections.template.content_place_original') ,'original'],
                [_('collections.template.content_place_in_tab') ,'in-tab'],
                [_('collections.template.content_place_original_except_children') ,'original-except-children'],
                [_('collections.template.content_place_none') ,'none']
            ]
        })
        ,displayField: 'd'
        ,valueField: 'v'
        ,mode: 'local'
        ,value: 'original'
        ,triggerAction: 'all'
        ,editable: false
        ,selectOnFocus: false
        ,preventRender: true
        ,forceSelection: true
        ,enableKeyEvents: true
    });
    Collections.combo.ContentPlace.superclass.constructor.call(this,config);
};
Ext.extend(Collections.combo.ContentPlace,MODx.combo.ComboBox);
Ext.reg('collections-combo-content-place',Collections.combo.ContentPlace);

Collections.combo.Resource = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        name: 'resource'
        ,hiddenName: 'resource'
        ,displayField: 'pagetitle'
        ,valueField: 'id'
        ,fields: ['pagetitle','id']
        ,pageSize: 20
        ,minChars: 1
        ,editable: true
        ,triggerAction: 'all'
        ,typeAhead: false
        ,forceSelection: true
        ,selectOnFocus: false
        ,url: Collections.config.connectorUrl
        ,baseParams:{
            action: 'mgr/extra/getresources'
        }
    });
    Collections.combo.Resource.superclass.constructor.call(this,config);
};
Ext.extend(Collections.combo.Resource,MODx.combo.ComboBox);
Ext.reg('collections-combo-resource',Collections.combo.Resource);


Collections.combo.ViewFor = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        store: new Ext.data.SimpleStore({
            fields: ['d','v']
            ,data: [
                [_('collections.template.view_for_all'), 0],
                [_('collections.template.view_for_collections'), 1],
                [_('collections.template.view_for_selections'), 2]
            ]
        })
        ,displayField: 'd'
        ,valueField: 'v'
        ,mode: 'local'
        ,value: 'view_for'
        ,triggerAction: 'all'
        ,editable: false
        ,selectOnFocus: false
        ,preventRender: true
        ,forceSelection: true
        ,enableKeyEvents: true
    });
    Collections.combo.ViewFor.superclass.constructor.call(this,config);
};
Ext.extend(Collections.combo.ViewFor,MODx.combo.ComboBox);
Ext.reg('collections-combo-view-for',Collections.combo.ViewFor);

Collections.combo.ExtendedBoolean = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        store: new Ext.data.SimpleStore({
            fields: ['d','v']
            ,data: [
                [_('collections.global.use_default'),null]
                ,[_('yes'),true]
                ,[_('no'),false]
            ]
        })
        ,displayField: 'd'
        ,valueField: 'v'
        ,mode: 'local'
        ,triggerAction: 'all'
        ,editable: false
        ,selectOnFocus: false
        ,preventRender: true
        ,forceSelection: true
        ,enableKeyEvents: true
    });
    Collections.combo.ExtendedBoolean.superclass.constructor.call(this,config);
};
Ext.extend(Collections.combo.ExtendedBoolean,MODx.combo.ComboBox);
Ext.reg('collections-combo-extended-boolean',Collections.combo.ExtendedBoolean);

Collections.combo.ContentType = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        name: 'content_type'
        ,hiddenName: 'content_type'
        ,displayField: 'name'
        ,valueField: 'id'
        ,fields: ['id', 'name']
        ,forceSelection: true
        ,typeAhead: false
        ,editable: false
        ,allowBlank: true
        ,pageSize: 20
        ,url: Collections.config.connectorUrl
        ,baseParams: {
            action: 'mgr/extra/getcontenttypes'
        }
    });
    Collections.combo.ContentType.superclass.constructor.call(this,config);
};
Ext.extend(Collections.combo.ContentType,MODx.combo.ComboBox);
Ext.reg('collections-combo-content-type',Collections.combo.ContentType);

Collections.combo.ContentDispositionExtended = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        store: new Ext.data.SimpleStore({
            fields: ['d','v']
            ,data: [
                [_('collections.global.use_default'),null]
                ,[_('inline'),0]
                ,[_('attachment'),1]
            ]
        })
        ,name: 'child_content_disposition'
        ,hiddenName: 'child_content_disposition'
        ,displayField: 'd'
        ,valueField: 'v'
        ,mode: 'local'
        ,triggerAction: 'all'
        ,editable: false
        ,pageSize: 20
        ,selectOnFocus: false
        ,preventRender: true
    });
    Collections.combo.ContentDispositionExtended.superclass.constructor.call(this,config);
};
Ext.extend(Collections.combo.ContentDispositionExtended,MODx.combo.ComboBox);
Ext.reg('collections-combo-content-disposition-extended',Collections.combo.ContentDispositionExtended);

Collections.combo.SortType = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        store: new Ext.data.SimpleStore({
            fields: ['d','v']
            ,data: [
                [_('collections.template.sort_type_default'), null]
                ,[_('collections.template.sort_type_integer'), 'SIGNED INTEGER']
                ,[_('collections.template.sort_type_decimal'), 'DECIMAL']
                ,[_('collections.template.sort_type_datetime'), 'DATETIME']
            ]
        })
        ,name: 'sort_type'
        ,hiddenName: 'sort_type'
        ,displayField: 'd'
        ,valueField: 'v'
        ,mode: 'local'
        ,triggerAction: 'all'
        ,editable: false
        ,pageSize: 20
        ,selectOnFocus: false
        ,preventRender: true
    });
    Collections.combo.SortType.superclass.constructor.call(this,config);
};
Ext.extend(Collections.combo.SortType,MODx.combo.ComboBox);
Ext.reg('collections-combo-sort-type',Collections.combo.SortType);