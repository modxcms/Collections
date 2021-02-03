<?php
namespace Collections\Model;

use xPDO\xPDO;

/**
 * Class SelectionContainer
 *
 *
 * @property \Collections\Model\CollectionSelection[] $Selection
 *
 * @package Collections\Model
 */
class SelectionContainer extends CollectionContainer
{
    public $showInContextMenu = true;
    public $allowDrop = 1;
    public $allowChildrenResources = false;

    function __construct(xPDO & $xpdo)
    {
        parent:: __construct($xpdo);
        $this->set('class_key', SelectionContainer::class);
    }

    public static function getControllerPath(xPDO &$modx)
    {
        return $modx->getOption('collections.core_path', null, $modx->getOption('core_path') . 'components/collections/') . 'controllers/selection/';
    }

    public function getContextMenuText()
    {
        $this->xpdo->lexicon->load('collections:default', 'collections:selections');

        return [
            'text_create' => $this->xpdo->lexicon('selections.system.text_create'),
            'text_create_here' => $this->xpdo->lexicon('selections.system.text_create_here'),
        ];
    }

    public function getResourceTypeName()
    {
        $this->xpdo->lexicon->load('collections:default', 'collections:selections');
        return $this->xpdo->lexicon('selections.system.type_name');
    }
}
