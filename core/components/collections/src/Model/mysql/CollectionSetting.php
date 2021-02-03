<?php
namespace Collections\Model\mysql;

use xPDO\xPDO;

class CollectionSetting extends \Collections\Model\CollectionSetting
{

    public static $metaMap = array (
        'package' => 'Collections\\Model\\',
        'version' => '3.0',
        'table' => 'collection_settings',
        'extends' => 'xPDO\\Om\\xPDOSimpleObject',
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
                'class' => 'Collections\\Model\\CollectionContainer',
                'local' => 'collection',
                'foreign' => 'id',
                'cardinality' => 'one',
                'owner' => 'foreign',
            ),
            'Template' => 
            array (
                'class' => 'Collections\\Model\\CollectionTemplate',
                'local' => 'template',
                'foreign' => 'id',
                'cardinality' => 'one',
                'owner' => 'local',
            ),
        ),
    );

}
