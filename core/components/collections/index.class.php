<?php
require_once dirname(__FILE__) . '/model/collections/collections.class.php';
/**
 * @package collections
 */
abstract class CollectionsBaseManagerController extends modExtraManagerController {
    /** @var Collections $collections */
    public $collections;
    public function initialize() {
        $this->collections = new Collections($this->modx);

        $this->addCss($this->collections->config['cssUrl'].'mgr.css');
        $this->addJavascript($this->collections->config['jsUrl'].'mgr/collections.js');
        $this->addHtml('<script type="text/javascript">
        Ext.onReady(function() {
            Collections.config = '.$this->modx->toJSON($this->collections->config).';
            Collections.config.connector_url = "'.$this->collections->config['connectorUrl'].'";
        });
        </script>');
        return parent::initialize();
    }
    public function getLanguageTopics() {
        return array('collections:default');
    }
    public function checkPermissions() { return true;}
}

class IndexManagerController extends CollectionsBaseManagerController {
    public static function getDefaultController() { return 'template'; }
}


