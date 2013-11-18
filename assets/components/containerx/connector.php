<?php
/**
 * ContainerX Connector
 *
 * @package containerx
 */
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
require_once MODX_CONNECTORS_PATH.'index.php';

$corePath = $modx->getOption('containerx.core_path',null,$modx->getOption('core_path').'components/containerx/');
require_once $corePath.'model/containerx/containerx.class.php';
$modx->containerx = new ContainerX($modx);

$modx->lexicon->load('containerx:default');

/* handle request */
$path = $modx->getOption('processorsPath',$modx->containerx->config,$corePath.'processors/');
$modx->request->handleRequest(array(
    'processors_path' => $path,
    'location' => '',
));
