<?php
$snippets = array();

$snippets[0]= $modx->newObject('modSnippet');
$snippets[0]->fromArray(array(
    'id' => 0,
    'name' => 'getSelections',
    'description' => '',
    'snippet' => getSnippetContent($sources['snippets'].'/getselections.snippet.php'),
),'',true,true);

return $snippets;