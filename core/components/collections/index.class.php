<?php
/**
 * @package collections
 */
abstract class CollectionsBaseManagerController extends modExtraManagerController
{
    /** @var \Collections\Collections $collections */
    public $collections;

    public function initialize()
    {
        $this->collections = $this->modx->services->get('collections');

        $this->addCss($this->collections->config['cssUrl'] . 'mgr.css');
        $this->addJavascript($this->collections->config['jsUrl'] . 'mgr/collections.js');

        $this->addHtml('<script type="text/javascript">
        Ext.onReady(function() {
            collections.config = ' . json_encode($this->collections->config) . ';
        });
        </script>');

        parent::initialize();
    }

    public function getLanguageTopics()
    {
        return ['collections:default'];
    }

    public function checkPermissions()
    {
        return true;
    }
}
