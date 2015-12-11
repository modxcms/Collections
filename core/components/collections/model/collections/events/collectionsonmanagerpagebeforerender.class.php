<?php
class CollectionsOnManagerPageBeforeRender extends CollectionsPlugin {

    public function run(){
        $this->scriptProperties['controller']->addLexiconTopic('collections:default');
        
        $this->scriptProperties['controller']->addHtml("
            <script>
            var collectionsToolbarLoaded = false;
            Ext.onReady(function() {
                var tree = Ext.getCmp('modx-resource-tree');
                var tb = (tree) ? tree.getTopToolbar() : false;
                if (!tb) return;
            
                var collectionsToolbar = false;
                tb.on('afterlayout', function(){
                    if (!collectionsToolbar && (this.items.length > 5)) {
                    
                    if (MODx.config['collections.tree_tbar_collection'] == 1) {
                        tb.insertButton(-3, {
                            cls: 'tree-collectioncontainer',
                            tooltip: _('collections.system.new_container'),
                            handler: function() {
                                MODx.loadPage('resource/create', 'class_key=CollectionContainer');
                            }
                        });
                    }
                    
                    if (MODx.config['collections.tree_tbar_selection'] == 1) {
                        tb.insertButton(-3, {
                            cls: 'tree-selectioncontainer',
                            tooltip: _('collections.system.new_selection_container'),
                            handler: function() {
                                MODx.loadPage('resource/create', 'class_key=SelectionContainer');
                            }
                        });
                    }
                    
                    collectionsToolbar = true;
                    tb.doLayout();
                    }
                });
            });
            </script>
        ");
        
        return true;
    }
}
