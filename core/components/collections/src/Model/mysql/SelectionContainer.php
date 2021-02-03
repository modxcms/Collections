<?php
namespace Collections\Model\mysql;

use xPDO\xPDO;

class SelectionContainer extends \Collections\Model\SelectionContainer
{

    public static $metaMap = array (
        'package' => 'Collections\\Model\\',
        'version' => '3.0',
        'extends' => 'Collections\\Model\\CollectionContainer',
        'tableMeta' => 
        array (
            'engine' => 'MyISAM',
        ),
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
                'class' => 'Collections\\Model\\CollectionSelection',
                'local' => 'id',
                'foreign' => 'collection',
                'cardinality' => 'many',
                'owner' => 'local',
            ),
        ),
    );

}
