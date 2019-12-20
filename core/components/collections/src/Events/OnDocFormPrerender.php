<?php
namespace Collections\Events;

use Collections\Model\CollectionContainer;
use Collections\Model\CollectionSelection;
use Collections\Model\CollectionTemplate;
use MODX\Revolution\modDocument;
use MODX\Revolution\modResource;
use MODX\Revolution\modStaticResource;
use MODX\Revolution\modSymLink;
use MODX\Revolution\modWebLink;

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

                $parent = $this->modx->getObject(modResource::class, $parent);
                if ($parent) {
                    $inject = ($parent->class_key == CollectionContainer::class);
                }
            }
        } else {
            $inject = ($parent->class_key == CollectionContainer::class && $resource->class_key != CollectionContainer::class);
        }

        if (!$inject && isset($_GET['selection']) && intval($_GET['selection'] > 0)) {
            $selection = $this->modx->getCount(CollectionSelection::class, ['resource' => $resource->id]);
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
                if ($grandParent->class_key == CollectionContainer::class) {
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
            $template = $this->collections->getCollectionsView($parent);

            $templateOptions = [
                'back_to_collection' => $template->back_to_collection_label,
                'back_to_selection' => $template->back_to_selection_label,
            ];

            $classKey = '';
            if (isset($_GET['class_key'])) {
                $classKey = $_GET['class_key'];
            } else {
                if ($resource) {
                    $classKey = $resource->class_key;
                }
            }

            if ($classKey == '') $classKey = modDocument::class;

            switch ($classKey) {
                case modDocument::class:
                    $classKey = 'Resource';
                    break;
                case modStaticResource::class:
                    $classKey = 'Static';
                    break;
                case modSymLink::class:
                    $classKey = 'SymLink';
                    break;
                case modWebLink::class:
                    $classKey = 'WebLink';
                    break;
                default:
                    $classKey = 'Resource';
            }


            $this->modx->regClientStartupHTMLBlock('
            <script type="text/javascript">
            Collections_labels = ' . json_encode($templateOptions) . ';
            Collections_mode = "' . ($this->scriptProperties['mode'] == 'new' ? 'Create' : 'Update') . '";
            Collections_type = "' . $classKey . '";
            Collections_collection_get = "' . $collectionGet . '";
            </script>');

            $jsUrl = $this->collections->getOption('jsUrl') . 'mgr/';
            $this->modx->regClientStartupScript($jsUrl . 'extra/hijackclose.js');
        }
    }
}
