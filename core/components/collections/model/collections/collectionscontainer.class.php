<?php
/**
 * @package collections
 */
class CollectionsContainer extends modResource {
    public $showInContextMenu = true;
    function __construct(xPDO & $xpdo) {
        parent :: __construct($xpdo);
        $this->set('class_key','CollectionsContainer');
    }

    public static function getControllerPath(xPDO &$modx) {
        return $modx->getOption('collections.core_path',null,$modx->getOption('core_path').'components/collections/').'controllers/';
    }

    public function getContextMenuText() {
        $this->xpdo->lexicon->load('collections:default');
        return array(
            'text_create' => $this->xpdo->lexicon('collections.system.text_create'),
            'text_create_here' => $this->xpdo->lexicon('collections.system.text_create_here'),
        );
    }

    public function getResourceTypeName() {
        $this->xpdo->lexicon->load('collections:default');
        return $this->xpdo->lexicon('collections.system.type_name');
    }
}
