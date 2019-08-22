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
        $defaultBlueprint = 0;

        if (($childTemplate > 0) && ($view->fred_default_blueprint > 0)) {
            /** @var \FredBlueprint $blueprint */
            $blueprint = $this->modx->getObject('FredBlueprint', ['uuid' => $view->fred_default_blueprint]);

            if ($blueprint) {
                $category = $blueprint->Category;
                if ($category) {
                        $themedTemplate = $this->modx->getCount('FredThemedTemplate', [
                            'theme' => $category->get('theme'),
                            'template' => $childTemplate
                        ]);

                        if ($themedTemplate === 1) {
                            $defaultBlueprint = $blueprint->id;
                        }
                }
            }
        }

        return $this->data([
            'template' => $childTemplate,
            'blueprint' => $defaultBlueprint,
            'sort' => $view->sort_field,
            'sortDir' => $view->sort_dir,
        ]);
    }
}
