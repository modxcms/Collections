<?php
require_once dirname(__FILE__) . '/collectioncontainer.class.php';

/**
 * @package collections
 */
class SelectionContainer extends CollectionContainer
{
    public $showInContextMenu = true;
    public $allowDrop = 1;
    public $allowChildrenResources = false;

    function __construct(xPDO & $xpdo)
    {
        parent:: __construct($xpdo);
        $this->set('class_key', 'SelectionContainer');
    }

    public static function getControllerPath(xPDO &$modx)
    {
        return $modx->getOption('collections.core_path', null, $modx->getOption('core_path') . 'components/collections/') . 'controllers/selection/';
    }

    public function getContextMenuText()
    {
        $this->xpdo->lexicon->load('collections:default', 'collections:selections');

        return array(
            'text_create' => $this->xpdo->lexicon('selections.system.text_create'),
            'text_create_here' => $this->xpdo->lexicon('selections.system.text_create_here'),
        );
    }

    public function getResourceTypeName()
    {
        $this->xpdo->lexicon->load('collections:default', 'collections:selections');
        return $this->xpdo->lexicon('selections.system.type_name');
    }
}

class SelectionContainerCreateProcessor extends CollectionContainerCreateProcessor
{
    public function afterSave()
    {
        return parent::afterSave();
    }
}

class SelectionContainerUpdateProcessor extends CollectionContainerUpdateProcessor
{
    public function afterSave()
    {
        return parent::afterSave();
    }
}
