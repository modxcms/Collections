<?php
/**
 * @package collections
 */
$xpdo_meta_map['CollectionSetting']= array (
  'package' => 'collections',
  'version' => NULL,
  'table' => 'collection_settings',
  'extends' => 'xPDOSimpleObject',
  'tableMeta' => 
  array (
    'engine' => 'MyISAM',
  ),
  'fields' => 
  array (
    'collection' => NULL,
    'template' => 0,
  ),
  'fieldMeta' => 
  array (
    'collection' => 
    array (
      'dbtype' => 'int',
      'attributes' => 'unsigned',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'unique',
    ),
    'template' => 
    array (
      'dbtype' => 'int',
      'attributes' => 'unsigned',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
  ),
  'aggregates' => 
  array (
    'Collection' => 
    array (
      'class' => 'CollectionContainer',
      'local' => 'collection',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Template' => 
    array (
      'class' => 'CollectionTemplate',
      'local' => 'template',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'local',
    ),
  ),
);
