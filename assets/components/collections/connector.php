<?php
/**
 * Collections Connector
 *
 * @package collections
 * 
 * @var modX $modx
 */
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';

$corePath = $modx->getOption('collections.core_path', null, $modx->getOption('core_path') . 'components/collections/');
require_once $corePath . 'model/collections/collections.class.php';
$modx->collections = new Collections($modx);

$modx->lexicon->load('collections:default');

/* handle request */
$path = $modx->getOption('processorsPath', $modx->collections->config, $corePath . 'processors/');
$modx->request->handleRequest(array(
    'processors_path' => $path,
    'location' => '',
));
