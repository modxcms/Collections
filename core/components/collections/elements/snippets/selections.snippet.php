<?php
/**
 * Selections
 *
 * DESCRIPTION
 *
 * This snippet is a helper for getResources call.
 * It will allows you to select all linked resources under specific Selection with a usage of getResources snippet.
 * Returns distinct list of linked Resources for given Selections
 *
 * PROPERTIES:
 *
 * &sortdir     string  optional    Direction of sorting by linked resource's menuindex. Default: ASC
 * &selections   string  optional    Comma separated list of Selection IDs for which should be retrieved linked resources. Default: empty string
 * $sort        integer optional    If set to 0, sortby property will not be generated and you can specify it manually in getResources call. Default: 1
 *
 * USAGE:
 *
 * [[getResources? [[!Selections? &selection=`1`]] &tpl=`rowTpl`]]
 * [[getResources? [[!Selections? &selection=`1` &sort=`0`]] &tpl=`rowTpl` &sortby=`RAND()`]]
 *
 */

$collections = $modx->getService('collections','Collections',$modx->getOption('collections.core_path',null,$modx->getOption('core_path').'components/collections/').'model/collections/',$scriptProperties);
if (!($collections instanceof Collections)) return '';

$sort = (int) $modx->getOption('sort', $scriptProperties, 1);
$sortDir = $modx->getOption('sortdir', $scriptProperties, 'asc');
$selections = $modx->getOption('selections', $scriptProperties, '');

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

if ($sort == 1) {
    $linkedResourcesQuery->sortby('menuindex', $sortDir);
}

$linkedResourcesQuery->select(array(
    'resource' => 'DISTINCT(resource)'
));

$linkedResourcesQuery->prepare();

$linkedResourcesQuery->stmt->execute();

$linkedResources = $linkedResourcesQuery->stmt->fetchAll(PDO::FETCH_COLUMN, 0);
$linkedResources = implode(',', $linkedResources);

$output = '&resources=`' . $linkedResources . '`';

if ($sort == 1) {
    $output .= ' &sortby=`FIELD(modResource.id, ' . $linkedResources . ' )` &sortdir=`asc`';
}

return $output;


