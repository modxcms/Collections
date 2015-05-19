<?php
/**
 * @package collections
 */
$xpdo_meta_map['CollectionResourceTemplate']= array (
  'package' => 'collections',
  'version' => NULL,
  'table' => 'collection_resource_template',
  'extends' => 'xPDOObject',
  'fields' => 
  array (
    'collection_template' => NULL,
    'resource_template' => NULL,
  ),
  'fieldMeta' => 
  array (
    'collection_template' => 
    array (
      'dbtype' => 'int',
      'attributes' => 'unsigned',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'pk',
    ),
    'resource_template' => 
    array (
      'dbtype' => 'int',
      'attributes' => 'unsigned',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'pk',
    ),
  ),
  'indexes' => 
  array (
    'PRIMARY' => 
    array (
      'alias' => 'PRIMARY',
      'primary' => true,
      'unique' => true,
      'type' => 'BTREE',
      'columns' => 
      array (
        'collection_template' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'resource_template' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'aggregates' => 
  array (
    'CollectionTemplate' => 
    array (
      'class' => 'CollectionTemplate',
      'local' => 'collection_template',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'ResourceTemplate' => 
    array (
      'class' => 'modTemplate',
      'local' => 'resource_template',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
