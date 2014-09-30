<?php
/**
 * @package collections
 */
$xpdo_meta_map['CollectionContainer']= array (
  'package' => 'collections',
  'version' => NULL,
  'extends' => 'modResource',
  'fields' => 
  array (
  ),
  'fieldMeta' => 
  array (
  ),
  'composites' => 
  array (
    'Setting' => 
    array (
      'class' => 'CollectionSetting',
      'local' => 'id',
      'foreign' => 'collection',
      'cardinality' => 'one',
      'owner' => 'local',
    ),
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
