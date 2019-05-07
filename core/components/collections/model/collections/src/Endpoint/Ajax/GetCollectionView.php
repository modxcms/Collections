<?php

namespace Collections\Endpoint\Ajax;

class GetCollectionView extends Endpoint
{
    protected $allowedMethod = ['GET', 'OPTIONS'];

    function process()
    {
        $collection = isset($_GET['collection']) ? intval($_GET['collection']) : 0;
        if (empty($collection)) return $this->failure('No Collection');

        /** @var \modResource $collection */
        $collection = $this->modx->getObject('modResource', ['id' => $collection]);
        if (!$collection) return $this->failure('No Collection');

        $view = $this->collections->getCollectionsView($collection);
        $templates = $this->fred->getFredTemplates();
        $templates = array_flip($templates);

        $childTemplate = isset($templates[$view->child_template]) ? $view->child_template : 0;

        return $this->data([
            'template' => $childTemplate
        ]);
    }


}
