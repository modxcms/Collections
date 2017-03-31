var Collections = function(config) {
    config = config || {};
    Collections.superclass.constructor.call(this,config);
};
Ext.extend(Collections,Ext.Component,{
    page:{},window:{},grid:{},tree:{},panel:{},combo:{},config: {},renderer: {},
    getPageUrl: function(action, parameters) {
        // Handles url, passed as first argument
        var parts = [];
        if (action) {
            if (isNaN(parseInt(action)) && (action.substr(0,1) == '?' || (action.substr(0, "index.php?".length) == 'index.php?'))) {
                parts.push(action);
            } else {
                parts.push('?a=' + action);
            }
        }
        
        if (parameters) {
            parts.push(parameters);
        }
        
        return parts.join('&');
    }
});
Ext.reg('collections',Collections);
collections = new Collections();
