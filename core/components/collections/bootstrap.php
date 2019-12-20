<?php
/**
 * @var \MODX\Revolution\modX $modx
 */

$modx->addPackage('Collections\Model', $namespace['path'] . 'src/', null, 'Collections\\');

$modx->services->add('collections', function($c) use ($modx) {
    return new Collections\Collections($modx);
});
