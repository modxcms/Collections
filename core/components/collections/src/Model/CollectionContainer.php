<?php
namespace Collections\Model;

use xPDO\xPDO;

/**
 * Class CollectionContainer
 *
 *
 * @property \Collections\Model\CollectionSetting $Setting
 *
 * @package Collections\Model
 */
class CollectionContainer extends \MODX\Revolution\modResource
{
    public $showInContextMenu = true;
    public $allowDrop = 1;
    public $allowChildrenResources = false;

    function __construct(xPDO & $xpdo)
    {
        parent:: __construct($xpdo);
        $this->set('class_key', CollectionContainer::class);
    }

    public static function getControllerPath(xPDO &$modx)
    {
        return $modx->getOption('collections.core_path', null, $modx->getOption('core_path') . 'components/collections/') . 'controllers/';
    }

    public function getContextMenuText()
    {
        $this->xpdo->lexicon->load('collections:default');
        return [
            'text_create' => $this->xpdo->lexicon('collections.system.text_create'),
            'text_create_here' => $this->xpdo->lexicon('collections.system.text_create_here'),
        ];
    }

    public function getResourceTypeName()
    {
        $this->xpdo->lexicon->load('collections:default');
        return $this->xpdo->lexicon('collections.system.type_name');
    }
}
