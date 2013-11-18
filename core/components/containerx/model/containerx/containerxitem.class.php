<?php
/**
 * @package containerx
 */
class ContainerXItem extends modResource {
    public $showInContextMenu = true;
    function __construct(xPDO & $xpdo) {
        parent :: __construct($xpdo);
        $this->set('class_key','ContainerX');
    }

    public static function getControllerPath(xPDO &$modx) {
        return $modx->getOption('containerx.core_path',null,$modx->getOption('core_path').'components/containerx/').'controllers/';
    }

    public function getContextMenuText() {
        $this->xpdo->lexicon->load('containerx:default');
        return array(
            'text_create' => $this->xpdo->lexicon('containerx.system.text_create'),
            'text_create_here' => $this->xpdo->lexicon('containerx.system.text_create_here'),
        );
    }

    public function getResourceTypeName() {
        $this->xpdo->lexicon->load('containerx:default');
        return $this->xpdo->lexicon('containerx.system.type_name');
    }
}
