<?php
/**
 * Collections
 *
 * DESCRIPTION
 *
 * This plugin inject JS to handle proper working of close buttons in Resource's panel (OnDocFormPrerender)
 * This plugin handles setting proper show_in_tree parameter (OnBeforeDocFormSave, OnResourceSort)
 *
 */
$corePath = $modx->getOption('collections.core_path', null, $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/collections/');
/** @var Collections $collections */
$collections = $modx->getService(
    'collections',
    'Collections',
    $corePath . 'model/collections/',
    array(
        'core_path' => $corePath
    )
);

$modx->loadClass('CollectionsPlugin', $collections->getOption('modelPath') . 'collections/events/', true, true);
$modx->loadClass($modx->event->name, $collections->getOption('modelPath') . 'collections/events/', true, true);

if (class_exists($modx->event->name)) {
    $handler = new $modx->event->name($modx, $scriptProperties);
    $handler->run();
}

return;