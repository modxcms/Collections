<?php
/**
 * Collections
 *
 * DESCRIPTION
 *
 * This plugin inject JS to handle proper working of close buttons in Resource's panel (OnDocFormPrerender)
 * This plugin handles setting proper show_in_tree parameter (OnBeforeDocFormSave, OnResourceSort)
 *
 * @var \MODX\Revolution\modX $modx
 * @var array $scriptProperties
 */

if (!$modx->services->has('collections')) {
    return;
}

/** @var Collections\Collections $collections */
$collections = $modx->services->get('collections');
if (!($collections instanceof Collections\Collections)) return '';

$className = "\\Collections\\Events\\{$modx->event->name}";
if (class_exists($className)) {
    /** @var \Collections\Events\Event $handler */
    $handler = new $className($modx, $scriptProperties);
    $handler->run();
}

return;