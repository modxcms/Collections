<?php

namespace Collections\Events;

class OnResourceDuplicate extends Event
{
    public function run()
    {
        /** @var \modResource $newResource */
        $newResource = $this->scriptProperties['newResource'];

        /** @var \modResource $oldResource */
        $oldResource = $this->scriptProperties['oldResource'];

        if ($oldResource->class_key !== 'CollectionContainer') return;

        /** @var \CollectionSetting $oldSettings */
        $oldSettings = $this->modx->getObject('CollectionSetting', ['collection' => $oldResource->id]);
        if ($oldSettings) {
            /** @var \CollectionSetting $newSettings */
            $newSettings = $this->modx->newObject('CollectionSetting');
            $newSettings->set('collection', $newResource->id);
            $newSettings->set('template', $oldSettings->template);
            $newSettings->save();
        }
    }
}
