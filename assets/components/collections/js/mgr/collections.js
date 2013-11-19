var Collections = function(config) {
    config = config || {};
    Collections.superclass.constructor.call(this,config);
};
Ext.extend(Collections,Ext.Component,{
    page:{},window:{},grid:{},tree:{},panel:{},combo:{},config: {}
});
Ext.reg('collections',Collections);
Collections = new Collections();
