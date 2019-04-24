<?php
require_once dirname(__FILE__) . '/model/collections/collections.class.php';

/**
 * @package collections
 */
abstract class CollectionsBaseManagerController extends modExtraManagerController
{
    /** @var Collections $collections */
    public $collections;

    public function initialize()
    {
        $corePath = $this->modx->getOption('collections.core_path', null, $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/collections/');
        $this->collections = $this->modx->getService(
            'collections',
            'Collections',
            $corePath . 'model/collections/',
            array(
                'core_path' => $corePath
            )
        );

        $this->addCss($this->collections->config['cssUrl'] . 'mgr.css');
        $this->addJavascript($this->collections->config['jsUrl'] . 'mgr/collections.js');

        $this->addHtml('<script type="text/javascript">
        Ext.onReady(function() {
            collections.config = ' . $this->modx->toJSON($this->collections->config) . ';
            collections.config.connector_url = "' . $this->collections->config['connectorUrl'] . '";
        });
        </script>');

        parent::initialize();
    }

    public function getLanguageTopics()
    {
        return array('collections:default');
    }

    public function checkPermissions()
    {
        return true;
    }
}