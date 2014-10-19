<?php
class OnDocFormPrerender extends CollectionsPlugin {

    public function run() {
        $inject = false;

        /** @var modResource $resource */
        $resource = $this->scriptProperties['resource'];
        /** @var modResource $parent */
        $parent = $resource->Parent;

        if (!$parent) {
            if (isset($_GET['parent'])) {
                $parent = intval($_GET['parent']);

                $parent = $this->modx->getObject('modResource', $parent);
                if ($parent){
                    $inject = ($parent->class_key == 'CollectionContainer');
                }
            }
        } else {
            $inject = ($parent->class_key == 'CollectionContainer' && $resource->class_key != 'CollectionContainer' && $resource->hasChildren() == 0);
        }

        if (!$inject && intval($_GET['selection'] > 0)) {
            $selection = $this->modx->getCount('CollectionSelection', array('resource' => $resource->id));
            if ($selection > 0) $inject = true;
        }

        if ($inject) {
            $jsUrl = $this->collections->getOption('jsUrl') . 'mgr/';
            $this->modx->regClientStartupScript($jsUrl . 'extra/hijackclose.js');
        }
    }
}