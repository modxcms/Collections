<?php
namespace Collections\Model\mysql;

use xPDO\xPDO;

class CollectionContainer extends \Collections\Model\CollectionContainer
{

    public static $metaMap = array (
        'package' => 'Collections\\Model\\',
        'version' => NULL,
        'extends' => 'MODX\\Revolution\\modResource',
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
            'Setting' => 
            array (
                'class' => 'Collections\\Model\\CollectionSetting',
                'local' => 'id',
                'foreign' => 'collection',
                'cardinality' => 'one',
                'owner' => 'local',
            ),
        ),
    );

}
