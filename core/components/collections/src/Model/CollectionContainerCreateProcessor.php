<?php
namespace Collections\Model;

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
