<?php
require_once MODX_CORE_PATH . 'model/modx/modprocessor.class.php';
require_once MODX_CORE_PATH . 'model/modx/processors/resource/create.class.php';
require_once MODX_CORE_PATH . 'model/modx/processors/resource/update.class.php';

/**
 * @package collections
 */
class CollectionContainer extends modResource
{
    public $showInContextMenu = true;
    public $allowDrop = 1;

    function __construct(xPDO & $xpdo)
    {
        parent:: __construct($xpdo);
        $this->set('class_key', 'CollectionContainer');
    }

    public static function getControllerPath(xPDO &$modx)
    {
        return $modx->getOption('collections.core_path', null, $modx->getOption('core_path') . 'components/collections/') . 'controllers/';
    }

    public function getContextMenuText()
    {
        $this->xpdo->lexicon->load('collections:default');
        return array(
            'text_create' => $this->xpdo->lexicon('collections.system.text_create'),
            'text_create_here' => $this->xpdo->lexicon('collections.system.text_create_here'),
        );
    }

    public function getResourceTypeName()
    {
        $this->xpdo->lexicon->load('collections:default');
        return $this->xpdo->lexicon('collections.system.type_name');
    }
}

class CollectionContainerCreateProcessor extends modResourceCreateProcessor
{
    public function afterSave()
    {
        $collectionsTemplate = $this->getProperty('collections_template');
        if ($collectionsTemplate === null) {
            return parent::afterSave();
        }

        $collectionsTemplate = (int)$collectionsTemplate;

        $config = $this->modx->getObject('CollectionSetting', array('collection' => $this->object->id));

        if (!$config) {
            $config = $this->modx->newObject('CollectionSetting');
            $config->set('collection', $this->object->id);
        }

        $config->set('template', $collectionsTemplate);

        $config->save();

        return parent::afterSave();
    }
}

class CollectionContainerUpdateProcessor extends modResourceUpdateProcessor
{
    public function afterSave()
    {
        $collectionsTemplate = $this->getProperty('collections_template');
        if ($collectionsTemplate === null) {
            return parent::afterSave();
        }

        $collectionsTemplate = (int)$collectionsTemplate;

        $config = $this->modx->getObject('CollectionSetting', array('collection' => $this->object->id));

        if (!$config) {
            $config = $this->modx->newObject('CollectionSetting');
            $config->set('collection', $this->object->id);
        }

        $config->set('template', $collectionsTemplate);

        $config->save();

        return parent::afterSave();
    }
}