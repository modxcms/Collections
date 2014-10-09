<?php
/**
 * @package collections
 */
$xpdo_meta_map['SelectionContainer']= array (
  'package' => 'collections',
  'version' => NULL,
  'extends' => 'CollectionContainer',
  'fields' => 
  array (
  ),
  'fieldMeta' => 
  array (
  ),
  'composites' => 
  array (
    'Selection' => 
    array (
      'class' => 'CollectionSelection',
      'local' => 'id',
      'foreign' => 'collection',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
