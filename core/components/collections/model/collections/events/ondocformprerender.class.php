<?php
class OnDocFormPrerender extends CollectionsPlugin {

    public function run() {
        $inject = false;

        /** @var \modResource $parent */
        $parent = $this->scriptProperties['resource']->Parent;
        if (!$parent) {
            if (isset($_GET['parent'])) {
                $parent = intval($_GET['parent']);

                $parent = $this->modx->getObject('modResource', $parent);
                if ($parent){
                    $inject = ($parent->class_key == 'CollectionContainer');
                }
            }
        } else {
            $inject = ($parent->class_key == 'CollectionContainer');
        }

        if (!$inject) {
            $selection = $this->modx->getCount('CollectionSelection', array('resource' => $this->scriptProperties['resource']->id));
            if ($selection > 0) $inject = true;
        }

        if ($inject) {
            $jsUrl = $this->collections->getOption('jsUrl') . 'mgr/';
            $this->modx->regClientStartupScript($jsUrl . 'extra/hijackclose.js');
        }
    }
}