<?php
namespace Collections\Events;

class OnDocFormPrerender extends Event
{

    public function run()
    {
        $inject = false;

        /** @var modResource $resource */
        $resource = isset($this->scriptProperties['resource']) ? $this->scriptProperties['resource'] : null;

        if ($resource) {
            /** @var modResource $parent */
            $parent = $resource->Parent;
        } else {
            $parent = null;
        }

        if (!$parent) {
            if (isset($_GET['parent'])) {
                $parent = intval($_GET['parent']);

                $parent = $this->modx->getObject('modResource', $parent);
                if ($parent) {
                    $inject = ($parent->class_key == 'CollectionContainer');
                }
            }
        } else {
            $inject = ($parent->class_key == 'CollectionContainer' && $resource->class_key != 'CollectionContainer');
        }

        if (!$inject && isset($_GET['selection']) && intval($_GET['selection'] > 0)) {
            $selection = $this->modx->getCount('CollectionSelection', array('resource' => $resource->id));
            if ($selection > 0) $inject = true;
        }

        if (!$inject && isset($_GET['collection']) && intval($_GET['collection'] > 0)) {
            $inject = true;
        }

        $collectionGet = null;
        $collectionFolder = null;
        if (!$inject && $parent) {
            $grandParent = $parent->Parent;
            while ($grandParent) {
                if ($grandParent->class_key == 'CollectionContainer') {
                    $collectionGet = $grandParent->id;
                    $inject = true;
                    break;
                }
                $grandParent = $grandParent->Parent;
            }
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

            $classKey = '';
            if (isset($_GET['class_key'])) {
                $classKey = $_GET['class_key'];
            } else {
                if ($resource) {
                    $classKey = $resource->class_key;
                }
            }

            if ($classKey == '') $classKey = 'modDocument';

            switch ($classKey) {
                case 'modDocument':
                    $classKey = 'Resource';
                    break;
                case 'modStaticResource':
                    $classKey = 'Static';
                    break;
                case 'modSymLink':
                    $classKey = 'SymLink';
                    break;
                case 'modWebLink':
                    $classKey = 'WebLink';
                    break;
                default:
                    $classKey = 'Resource';
            }


            $this->modx->regClientStartupHTMLBlock('
            <script type="text/javascript">
            Collections_labels = ' . $this->modx->toJSON($templateOptions) . ';
            Collections_mode = "' . ($this->scriptProperties['mode'] == 'new' ? 'Create' : 'Update') . '";
            Collections_type = "' . $classKey . '";
            Collections_collection_get = "' . $collectionGet . '";
            </script>');

            $jsUrl = $this->collections->getOption('jsUrl') . 'mgr/';
            $this->modx->regClientStartupScript($jsUrl . 'extra/hijackclose.js');
        }
    }
}