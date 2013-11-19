<?php
/**
 * Properties for the Collections snippet.
 *
 * @package collections
 * @subpackage build
 */
$properties = array(
    array(
        'name' => 'tpl',
        'desc' => 'prop_collections.tpl_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 'Item',
        'lexicon' => 'collections:properties',
    ),
    array(
        'name' => 'sortBy',
        'desc' => 'prop_collections.sortby_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 'name',
        'lexicon' => 'collections:properties',
    ),
    array(
        'name' => 'sortDir',
        'desc' => 'prop_collections.sortdir_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 'ASC',
        'lexicon' => 'collections:properties',
    ),
    array(
        'name' => 'limit',
        'desc' => 'prop_collections.limit_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 5,
        'lexicon' => 'collections:properties',
    ),
    array(
        'name' => 'outputSeparator',
        'desc' => 'prop_collections.outputseparator_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'collections:properties',
    ),
    array(
        'name' => 'toPlaceholder',
        'desc' => 'prop_collections.toplaceholder_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => true,
        'lexicon' => 'collections:properties',
    ),
/*
    array(
        'name' => '',
        'desc' => 'prop_collections.',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'collections:properties',
    ),
    */
);

return $properties;
