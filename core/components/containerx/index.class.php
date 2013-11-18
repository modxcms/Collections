<?php
require_once dirname(__FILE__) . '/model/containerx/containerx.class.php';
/**
 * @package containerx
 */
abstract class ContainerXBaseManagerController extends modExtraManagerController {
    /** @var ContainerX $containerx */
    public $containerx;
    public function initialize() {
        $this->containerx = new ContainerX($this->modx);

        $this->addCss($this->containerx->config['cssUrl'].'mgr.css');
        $this->addJavascript($this->containerx->config['jsUrl'].'mgr/containerx.js');
        $this->addHtml('<script type="text/javascript">
        Ext.onReady(function() {
            ContainerX.config = '.$this->modx->toJSON($this->containerx->config).';
            ContainerX.config.connector_url = "'.$this->containerx->config['connectorUrl'].'";
        });
        </script>');
        return parent::initialize();
    }
    public function getLanguageTopics() {
        return array('containerx:default');
    }
    public function checkPermissions() { return true;}
}

class IndexManagerController extends ContainerXBaseManagerController {
    public static function getDefaultController() { return 'home'; }
}


