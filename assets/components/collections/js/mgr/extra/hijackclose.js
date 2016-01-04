Ext.override(MODx.page[Collections_mode + Collections_type], {
    collectionsOriginals: {
        getButtons: MODx.page[Collections_mode + Collections_type].prototype.getButtons
    }
    ,getButtons: function(config) {
        var buttons = this.collectionsOriginals.getButtons.call(this, config);

        if (MODx.request.selection) {
            buttons.unshift({
                text: _(Collections_labels.back_to_selection) ? _(Collections_labels.back_to_selection) : Collections_labels.back_to_selection
                ,handler: function() {
                    MODx.loadPage(MODx.action['resource/update'], 'id=' + MODx.request.selection);
                }
            });
        } else {
            buttons.unshift({
                text: _(Collections_labels.back_to_collection) ? _(Collections_labels.back_to_collection) : Collections_labels.back_to_collection
                ,handler: function() {
                    var targetCollection = (Collections_collection_get || MODx.request.collection || config.record.parent);
                    
                    var folder = parseInt(MODx.request.folder);
                    if (folder > 0) {
                        folder = '&folder=' + folder;
                    } else {
                        var parent = parseInt(MODx.request.parent);
                     
                        if ((parent > 0) && (targetCollection != parent)) {
                            folder = '&folder=' + parent;  
                        } else {
                            folder = '';
                        }
                    }
                    
                    MODx.loadPage(MODx.action['resource/update'], 'id=' + targetCollection + folder);
                }
            });
        }

        return buttons;
    }
});