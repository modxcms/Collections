<?php
namespace Collections\Model\mysql;

use xPDO\xPDO;

class CollectionSelection extends \Collections\Model\CollectionSelection
{

    public static $metaMap = array (
        'package' => 'Collections\\Model\\',
        'version' => '3.0',
        'table' => 'collection_selections',
        'tableMeta' => 
        array (
            'engine' => 'MyISAM',
        ),
        'fields' => 
        array (
            'collection' => NULL,
            'resource' => NULL,
            'menuindex' => 0,
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
                'index' => 'pk',
            ),
            'resource' => 
            array (
                'dbtype' => 'int',
                'attributes' => 'unsigned',
                'precision' => '10',
                'phptype' => 'integer',
                'null' => false,
                'index' => 'pk',
            ),
            'menuindex' => 
            array (
                'dbtype' => 'int',
                'attributes' => 'unsigned',
                'precision' => '10',
                'phptype' => 'integer',
                'null' => false,
                'default' => 0,
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
                    'collection' => 
                    array (
                        'length' => '',
                        'collation' => 'A',
                        'null' => false,
                    ),
                    'resource' => 
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
            'Collection' => 
            array (
                'class' => 'Collections\\Model\\CollectionContainer',
                'local' => 'collection',
                'foreign' => 'id',
                'cardinality' => 'one',
                'owner' => 'foreign',
            ),
            'CollectionSetting' => 
            array (
                'class' => 'Collections\\Model\\CollectionSetting',
                'local' => 'collection',
                'foreign' => 'id',
                'cardinality' => 'one',
                'owner' => 'foreign',
            ),
            'Resource' => 
            array (
                'class' => 'MODX\\Revolution\\modResource',
                'local' => 'resource',
                'foreign' => 'id',
                'cardinality' => 'one',
                'owner' => 'foreign',
            ),
        ),
    );

}
