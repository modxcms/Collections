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
 *
 * @var \MODX\Revolution\modX $modx
 * @var array $scriptProperties
 */

use Collections\Model\CollectionSelection;
use Collections\Utils;
use MODX\Revolution\modSnippet;

/** @var Collections\Collections $collections */
$collections = $modx->services->get('collections');
if (!($collections instanceof Collections\Collections)) return '';

$getResourcesSnippet = $modx->getOption('getResourcesSnippet', $scriptProperties, 'getResources');

$getResourcesExists = $modx->getCount(modSnippet::class, ['name' => $getResourcesSnippet]);
if ($getResourcesExists == 0) return 'getResources not found';

$sortDir = strtolower($modx->getOption('sortdir', $scriptProperties, 'asc'));
$selections = $modx->getOption('selections', $scriptProperties, '');
$sortBy = $modx->getOption('sortby', $scriptProperties, '');
$excludeToPlaceholder = $modx->getOption('excludeToPlaceholder', $scriptProperties, '');

$selections = Utils::explodeAndClean($selections);

if ($sortDir != 'asc') {
    $sortDir = 'desc';
}

$linkedResourcesQuery = $modx->newQuery(CollectionSelection::class);

if (!empty($selections)) {
    $linkedResourcesQuery->where([
        'collection:IN' => $selections
    ]);
}

if ($sortBy == '') {
    $linkedResourcesQuery->sortby('menuindex', $sortDir);
}

$linkedResourcesQuery->select([
    'resource' => 'DISTINCT(resource)',
    'menuindex' => 'menuindex'
]);

$linkedResourcesQuery->prepare();

$linkedResourcesQuery->stmt->execute();

$linkedResources = $linkedResourcesQuery->stmt->fetchAll(PDO::FETCH_COLUMN, 0);

if (!empty($excludeToPlaceholder)) {
    $excludeResources = [];
    foreach($linkedResources as $res) {
        $excludeResources[] = '-' . $res;
    }
    $excludeResources = implode(',', $excludeResources);
    $modx->setPlaceholder($excludeToPlaceholder, $excludeResources);
}

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
