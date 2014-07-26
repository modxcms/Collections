<?php
/**
 * @package collections
 */
$xpdo_meta_map['CollectionTemplate']= array (
  'package' => 'collections',
  'version' => NULL,
  'table' => 'collection_templates',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'name' => NULL,
    'description' => '',
    'global_template' => 0,
    'bulk_actions' => 0,
    'allow_dd' => 1,
    'page_size' => 20,
    'sort_field' => 'id',
    'sort_dir' => 'asc',
    'child_template' => NULL,
    'child_resource_type' => 'modDocument',
    'resource_type_selection' => 1,
  ),
  'fieldMeta' => 
  array (
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '50',
      'phptype' => 'string',
      'null' => false,
      'index' => 'unique',
    ),
    'description' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'global_template' => 
    array (
      'dbtype' => 'int',
      'precision' => '1',
      'phptype' => 'boolean',
      'null' => false,
      'default' => 0,
    ),
    'bulk_actions' => 
    array (
      'dbtype' => 'int',
      'precision' => '1',
      'phptype' => 'boolean',
      'null' => false,
      'default' => 0,
    ),
    'allow_dd' => 
    array (
      'dbtype' => 'int',
      'precision' => '1',
      'phptype' => 'boolean',
      'null' => false,
      'default' => 1,
    ),
    'page_size' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 20,
    ),
    'sort_field' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => false,
      'default' => 'id',
    ),
    'sort_dir' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '4',
      'phptype' => 'string',
      'null' => false,
      'default' => 'asc',
    ),
    'child_template' => 
    array (
      'dbtype' => 'int',
      'attributes' => 'unsigned',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => true,
    ),
    'child_resource_type' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => false,
      'default' => 'modDocument',
    ),
    'resource_type_selection' => 
    array (
      'dbtype' => 'int',
      'precision' => '1',
      'phptype' => 'boolean',
      'null' => false,
      'default' => 1,
    ),
  ),
  'composites' => 
  array (
    'Columns' => 
    array (
      'class' => 'CollectionTemplateColumn',
      'local' => 'id',
      'foreign' => 'template',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'ResourceTemplates' => 
    array (
      'class' => 'CollectionResourceTemplate',
      'local' => 'id',
      'foreign' => 'collection_template',
      'cardinality' => 'one',
      'owner' => 'local',
    ),
  ),
  'aggregates' => 
  array (
    'Setting' => 
    array (
      'class' => 'CollectionSetting',
      'local' => 'id',
      'foreign' => 'template',
      'cardinality' => 'one',
      'owner' => 'local',
    ),
  ),
);
