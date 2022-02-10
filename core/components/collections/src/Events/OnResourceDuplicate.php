<?php

namespace Collections\Events;

use  Collections\Model\CollectionContainer;
use Collections\Model\CollectionSetting;

class OnResourceDuplicate extends Event
{
    public function run()
    {
        /** @var modResource $newResource */
        $newResource = $this->scriptProperties['newResource'];

        /** @var modResource $oldResource */
        $oldResource = $this->scriptProperties['oldResource'];

        if ($oldResource->class_key !== CollectionContainer::class) return;

        /** @var CollectionSetting $oldSettings */
        $oldSettings = $this->modx->getObject(CollectionSetting::class, ['collection' => $oldResource->id]);
        if ($oldSettings) {
            /** @var CollectionSetting $newSettings */
            $newSettings = $this->modx->newObject(CollectionSetting::class);
            $newSettings->set('collection', $newResource->id);
            $newSettings->set('template', $oldSettings->template);
            $newSettings->save();
        }
    }
}
