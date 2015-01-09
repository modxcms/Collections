<?php
class CollectionsOnDocFormPrerender extends CollectionsPlugin {

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

        if (!$inject && isset($_GET['selection']) && intval($_GET['selection'] > 0)) {
            $selection = $this->modx->getCount('CollectionSelection', array('resource' => $resource->id));
            if ($selection > 0) $inject = true;
        }

        if ($inject) {
            $this->modx->controller->addLexiconTopic('collections:default');
            $this->modx->controller->addLexiconTopic('collections:selections');
            $this->modx->controller->addLexiconTopic('collections:custom');

            /** @var CollectionTemplate $template */
            $template = $this->modx->collections->getCollectionsView($parent);

            $templateOptions = array(
                'back_to_collection' => $template->back_to_collection_label,
                'back_to_selection' => $template->back_to_selection_label,
            );

            $this->modx->regClientStartupHTMLBlock('
            <script type="text/javascript">
            Collections_labels = ' . $this->modx->toJSON($templateOptions) . ';
            </script>');

            $jsUrl = $this->collections->getOption('jsUrl') . 'mgr/';
            $this->modx->regClientStartupScript($jsUrl . 'extra/hijackclose.js');
        }
    }
}