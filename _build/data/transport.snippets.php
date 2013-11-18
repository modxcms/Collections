<?php
/**
 * Add snippets to build
 * 
 * @package containerx
 * @subpackage build
 */
$snippets = array();

$snippets[0]= $modx->newObject('modSnippet');
$snippets[0]->fromArray(array(
    'id' => 0,
    'name' => 'ContainerX',
    'description' => 'Displays Items.',
    'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/snippet.containerx.php'),
),'',true,true);
$properties = include $sources['build'].'properties/properties.containerx.php';
$snippets[0]->setProperties($properties);
unset($properties);

return $snippets;
