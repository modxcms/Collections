<?php
/**
 * Properties for the ContainerX snippet.
 *
 * @package containerx
 * @subpackage build
 */
$properties = array(
    array(
        'name' => 'tpl',
        'desc' => 'prop_containerx.tpl_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 'Item',
        'lexicon' => 'containerx:properties',
    ),
    array(
        'name' => 'sortBy',
        'desc' => 'prop_containerx.sortby_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 'name',
        'lexicon' => 'containerx:properties',
    ),
    array(
        'name' => 'sortDir',
        'desc' => 'prop_containerx.sortdir_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 'ASC',
        'lexicon' => 'containerx:properties',
    ),
    array(
        'name' => 'limit',
        'desc' => 'prop_containerx.limit_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 5,
        'lexicon' => 'containerx:properties',
    ),
    array(
        'name' => 'outputSeparator',
        'desc' => 'prop_containerx.outputseparator_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'containerx:properties',
    ),
    array(
        'name' => 'toPlaceholder',
        'desc' => 'prop_containerx.toplaceholder_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => true,
        'lexicon' => 'containerx:properties',
    ),
/*
    array(
        'name' => '',
        'desc' => 'prop_containerx.',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'containerx:properties',
    ),
    */
);

return $properties;
