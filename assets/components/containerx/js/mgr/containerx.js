var ContainerX = function(config) {
    config = config || {};
    ContainerX.superclass.constructor.call(this,config);
};
Ext.extend(ContainerX,Ext.Component,{
    page:{},window:{},grid:{},tree:{},panel:{},combo:{},config: {}
});
Ext.reg('containerx',ContainerX);
ContainerX = new ContainerX();
