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
    'tab_label' => 'collections.children',
    'button_label' => 'collections.children.create',
    'content_place' => 'original',
    'view_for' => 0,
    'link_label' => 'selections.create',
    'context_menu' => 'view,edit,duplicate,publish,unpublish,-,delete,undelete,remove,-,unlink',
    'buttons' => 'view,edit,duplicate,publish:orange,unpublish,delete,undelete,remove,unlink',
    'allowed_resource_types' => '',
    'back_to_collection_label' => 'collections.children.back_to_collection_label',
    'back_to_selection_label' => 'selections.back_to_selection_label',
    'selection_create_sort' => 'id:desc',
    'child_hide_from_menu' => NULL,
    'child_published' => NULL,
    'child_cacheable' => NULL,
    'child_searchable' => NULL,
    'child_richtext' => NULL,
    'child_content_type' => 0,
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
    'tab_label' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => 'collections.children',
    ),
    'button_label' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => 'collections.children.create',
    ),
    'content_place' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => 'original',
    ),
    'view_for' => 
    array (
      'dbtype' => 'int',
      'precision' => '1',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 0,
    ),
    'link_label' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => 'selections.create',
    ),
    'context_menu' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '512',
      'phptype' => 'string',
      'null' => false,
      'default' => 'view,edit,duplicate,publish,unpublish,-,delete,undelete,remove,-,unlink',
    ),
    'buttons' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '512',
      'phptype' => 'string',
      'null' => false,
      'default' => 'view,edit,duplicate,publish:orange,unpublish,delete,undelete,remove,unlink',
    ),
    'allowed_resource_types' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '512',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'back_to_collection_label' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => 'collections.children.back_to_collection_label',
    ),
    'back_to_selection_label' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => 'selections.back_to_selection_label',
    ),
    'selection_create_sort' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => 'id:desc',
    ),
    'child_hide_from_menu' => 
    array (
      'dbtype' => 'int',
      'precision' => '1',
      'phptype' => 'boolean',
      'null' => true,
    ),
    'child_published' => 
    array (
      'dbtype' => 'int',
      'precision' => '1',
      'phptype' => 'boolean',
      'null' => true,
    ),
    'child_cacheable' => 
    array (
      'dbtype' => 'int',
      'precision' => '1',
      'phptype' => 'boolean',
      'null' => true,
    ),
    'child_searchable' => 
    array (
      'dbtype' => 'int',
      'precision' => '1',
      'phptype' => 'boolean',
      'null' => true,
    ),
    'child_richtext' => 
    array (
      'dbtype' => 'int',
      'precision' => '1',
      'phptype' => 'boolean',
      'null' => true,
    ),
    'child_content_type' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
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
