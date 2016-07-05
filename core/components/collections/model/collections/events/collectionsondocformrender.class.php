<?php

class CollectionsOnDocFormRender extends CollectionsPlugin
{

    public function run()
    {
        if (empty($this->scriptProperties['mode']) || $this->scriptProperties['mode'] !== 'new') {
            return;
        }

        /** @var modResource $parent */
        $parent = $this->scriptProperties['resource']->Parent;

        if (empty($parent) || $parent->class_key != 'CollectionContainer') {
            return;
        }

        /** @var CollectionTemplate $template */
        $template = $this->modx->collections->getCollectionsView($parent);

        $this->setConfig('hidemenu_default', $template->child_hide_from_menu);
        $this->setConfig('publish_default', $template->child_published);
        $this->setConfig('cache_default', $template->child_cacheable);
        $this->setConfig('richtext_default', $template->child_richtext);
        $this->setConfig('search_default', $template->child_searchable);
        $this->setConfig('default_content_type', $template->child_content_type, 0);

        $this->modx->regClientStartupHTMLBlock('
        <script type="text/javascript">
            Ext.onReady(function() {
                ' . $this->setJSConfig('modx-resource-content-dispo', $template->child_content_disposition) . '
            });
        </script>');
    }

    private function setConfig($name, $value, $default = null)
    {
        if ($value !== $default) {
            $this->modx->_userConfig[$name] = intval($value);
        }
    }

    private function setJSConfig($name, $value, $default = null)
    {
        if ($value !== $default) {
            return 'if (Ext.getCmp("' . $name . '")) {Ext.getCmp("' . $name . '").setValue(' . intval($value) . ');}';
        }

        return '';
    }
}