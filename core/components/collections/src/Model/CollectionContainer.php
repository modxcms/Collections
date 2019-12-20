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
        $this->set('class_key', 'CollectionContainer');
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

class CollectionContainerCreateProcessor extends \MODX\Revolution\Processors\Resource\Create
{
    public function afterSave()
    {
        $collectionsTemplate = $this->getProperty('collections_template');
        if ($collectionsTemplate === null) {
            return parent::afterSave();
        }

        $collectionsTemplate = (int)$collectionsTemplate;

        $config = $this->modx->getObject(CollectionSetting::class, ['collection' => $this->object->id]);

        if (!$config) {
            $config = $this->modx->newObject(CollectionSetting::class);
            $config->set('collection', $this->object->id);
        }

        $config->set('template', $collectionsTemplate);

        $config->save();

        return parent::afterSave();
    }
}

class CollectionContainerUpdateProcessor extends \MODX\Revolution\Processors\Resource\Update
{
    public function afterSave()
    {
        $collectionsTemplate = $this->getProperty('collections_template');
        if ($collectionsTemplate === null) {
            return parent::afterSave();
        }

        $collectionsTemplate = (int)$collectionsTemplate;

        $config = $this->modx->getObject(CollectionSetting::class, ['collection' => $this->object->id]);

        if (!$config) {
            $config = $this->modx->newObject(CollectionSetting::class);
            $config->set('collection', $this->object->id);
        }

        $config->set('template', $collectionsTemplate);

        $config->save();

        return parent::afterSave();
    }
}
