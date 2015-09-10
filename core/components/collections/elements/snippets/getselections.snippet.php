<?php
/**
 * getSelections
 *
 * DESCRIPTION
 *
 * This snippet is a helper for getResources call.
 * It will allows you to select all linked resources under specific Selection with a usage of getResources snippet.
 * Returns distinct list of linked Resources for given Selections
 *
 * getResources are required
 *
 * PROPERTIES:
 *
 * &sortdir                 string  optional    Direction of sorting by linked resource's menuindex. Default: ASC
 * &selections              string  optional    Comma separated list of Selection IDs for which should be retrieved linked resources. Default: empty string
 * &getResourcesSnippet     string  optional    Name of getResources snippet. Default: getResources
 *
 * USAGE:
 *
 * [[getSelections? &selections=`1` &tpl=`rowTpl`]]
 * [[getSelections? &selections=`1` &tpl=`rowTpl` &sortby=`RAND()`]]
 *
 */

$collections = $modx->getService('collections','Collections',$modx->getOption('collections.core_path',null,$modx->getOption('core_path').'components/collections/').'model/collections/',$scriptProperties);
if (!($collections instanceof Collections)) return '';

$getResourcesSnippet = $modx->getOption('getResourcesSnippet', $scriptProperties, 'getResources');

$getResourcesExists = $modx->getCount('modSnippet', array('name' => $getResourcesSnippet));
if ($getResourcesExists == 0) return 'getResources not found';

$sortDir = strtolower($modx->getOption('sortdir', $scriptProperties, 'asc'));
$selections = $modx->getOption('selections', $scriptProperties, '');
$sortBy = $modx->getOption('sortby', $scriptProperties, '');

$selections = $modx->collections->explodeAndClean($selections);

if ($sortDir != 'asc') {
    $sortDir = 'desc';
}

$linkedResourcesQuery = $modx->newQuery('CollectionSelection');

if (!empty($selections)) {
    $linkedResourcesQuery->where(array(
        'collection:IN' => $selections
    ));
}

if ($sortBy == '') {
    $linkedResourcesQuery->sortby('menuindex', $sortDir);
}

$linkedResourcesQuery->select(array(
    'resource' => 'DISTINCT(resource)'
));

$linkedResourcesQuery->prepare();

$linkedResourcesQuery->stmt->execute();

$linkedResources = $linkedResourcesQuery->stmt->fetchAll(PDO::FETCH_COLUMN, 0);
$linkedResources = implode(',', $linkedResources);

$properties = $scriptProperties;
unset($properties['selections']);

$properties['resources'] = $linkedResources;
$properties['parents'] = ($properties['getResourcesSnippet'] == 'pdoResources') ? 0 : -1;

if ($sortBy == '') {
    $properties['sortby'] = 'FIELD(modResource.id, ' . $linkedResources . ' )';
    $properties['sortdir'] = 'asc';
}

return $modx->runSnippet($getResourcesSnippet, $properties);
