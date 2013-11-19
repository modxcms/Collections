<?php
/**
 * Add snippets to build
 * 
 * @package collections
 * @subpackage build
 */
$snippets = array();

$snippets[0]= $modx->newObject('modSnippet');
$snippets[0]->fromArray(array(
    'id' => 0,
    'name' => 'Collections',
    'description' => 'Displays Items.',
    'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/snippet.collections.php'),
),'',true,true);
$properties = include $sources['build'].'properties/properties.collections.php';
$snippets[0]->setProperties($properties);
unset($properties);

return $snippets;
