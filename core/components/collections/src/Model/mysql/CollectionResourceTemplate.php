<?php
namespace Collections\Model\mysql;

use xPDO\xPDO;

class CollectionResourceTemplate extends \Collections\Model\CollectionResourceTemplate
{

    public static $metaMap = array (
        'package' => 'Collections\\Model\\',
        'version' => '3.0',
        'table' => 'collection_resource_template',
        'tableMeta' => 
        array (
            'engine' => 'MyISAM',
        ),
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
                'class' => 'Collections\\Model\\CollectionTemplate',
                'local' => 'collection_template',
                'foreign' => 'id',
                'cardinality' => 'one',
                'owner' => 'foreign',
            ),
            'ResourceTemplate' => 
            array (
                'class' => 'MODX\\Revolution\\modTemplate',
                'local' => 'resource_template',
                'foreign' => 'id',
                'cardinality' => 'one',
                'owner' => 'foreign',
            ),
        ),
    );

}
