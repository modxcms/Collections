<?php
namespace Collections\Model\mysql;

use xPDO\xPDO;

class CollectionTemplateColumn extends \Collections\Model\CollectionTemplateColumn
{

    public static $metaMap = array (
        'package' => 'Collections\\Model\\',
        'version' => '3.0',
        'table' => 'collection_template_columns',
        'extends' => 'xPDO\\Om\\xPDOSimpleObject',
        'tableMeta' => 
        array (
            'engine' => 'MyISAM',
        ),
        'fields' => 
        array (
            'template' => NULL,
            'label' => NULL,
            'name' => NULL,
            'hidden' => 0,
            'sortable' => 0,
            'width' => 100,
            'editor' => '',
            'renderer' => '',
            'php_renderer' => '',
            'position' => 0,
            'sort_type' => NULL,
        ),
        'fieldMeta' => 
        array (
            'template' => 
            array (
                'dbtype' => 'int',
                'attributes' => 'unsigned',
                'precision' => '10',
                'phptype' => 'integer',
                'null' => false,
            ),
            'label' => 
            array (
                'dbtype' => 'varchar',
                'precision' => '50',
                'phptype' => 'string',
                'null' => false,
            ),
            'name' => 
            array (
                'dbtype' => 'varchar',
                'precision' => '50',
                'phptype' => 'string',
                'null' => false,
            ),
            'hidden' => 
            array (
                'dbtype' => 'tinyint',
                'attributes' => 'unsigned',
                'precision' => '1',
                'phptype' => 'boolean',
                'null' => false,
                'default' => 0,
            ),
            'sortable' => 
            array (
                'dbtype' => 'tinyint',
                'attributes' => 'unsigned',
                'precision' => '1',
                'phptype' => 'boolean',
                'null' => false,
                'default' => 0,
            ),
            'width' => 
            array (
                'dbtype' => 'int',
                'attributes' => 'unsigned',
                'precision' => '10',
                'phptype' => 'integer',
                'null' => false,
                'default' => 100,
            ),
            'editor' => 
            array (
                'dbtype' => 'varchar',
                'precision' => '255',
                'phptype' => 'string',
                'null' => false,
                'default' => '',
            ),
            'renderer' => 
            array (
                'dbtype' => 'varchar',
                'precision' => '255',
                'phptype' => 'string',
                'null' => false,
                'default' => '',
            ),
            'php_renderer' => 
            array (
                'dbtype' => 'varchar',
                'precision' => '255',
                'phptype' => 'string',
                'null' => false,
                'default' => '',
            ),
            'position' => 
            array (
                'dbtype' => 'int',
                'attributes' => 'unsigned',
                'precision' => '10',
                'phptype' => 'integer',
                'null' => false,
                'default' => 0,
            ),
            'sort_type' => 
            array (
                'dbtype' => 'varchar',
                'precision' => '64',
                'phptype' => 'string',
                'null' => true,
            ),
        ),
        'aggregates' => 
        array (
            'Template' => 
            array (
                'class' => 'Collections\\Model\\CollectionTemplate',
                'local' => 'template',
                'foreign' => 'id',
                'cardinality' => 'one',
                'owner' => 'foreign',
            ),
        ),
    );

}
