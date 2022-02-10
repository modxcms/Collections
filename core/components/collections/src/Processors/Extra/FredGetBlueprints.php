<?php
namespace Collections\Processors\Extra;

use MODX\Revolution\Processors\Model\GetListProcessor;
use xPDO\Om\xPDOQuery;


class FredGetBlueprints extends GetListProcessor
{
    public $classKey = 'Fred\\Model\\FredBlueprint';
    public $defaultSortField = 'name';
    public $defaultSortDirection = 'asc';
    public $checkListPermission = true;
    public $languageTopics = ['fred:default'];

    public function initialize()
    {
        if (!$this->modx->services->has('fred')) {
            return false;
        }

        $this->setDefaultProperties([
            'start' => 0,
            'limit' => 20,
            'query' => '',
        ]);

        return parent::initialize();
    }

    public function beforeIteration(array $list)
    {
        $addNone = (int)$this->getProperty('addNone', 0);

        if ($addNone === 1) {
            $list[] = [
                'id' => '',
                'name' => $this->modx->lexicon('fred.global.none')
            ];
        }

        return parent::beforeIteration($list);
    }

    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $uuid = $this->getProperty('uuid', '');
        if (!empty($uuid)) {
            $c->where(['uuid' => $uuid]);
        }

        $query = $this->getProperty('query', '');
        if (!empty($query)) {
            $c->where(['name:LIKE' => "%{$query}%"]);
        }

        $c->where([
            'public' => true,
            'Category.public' => true
        ]);

        $template = (int)$this->getProperty('template', 0);
        if (!empty($template)) {
            $c->where([
                'Templates.template' => $template
            ]);
        }

        return parent::prepareQueryBeforeCount($c);
    }

    public function prepareQueryAfterCount(xPDOQuery $c)
    {
        $c->leftJoin('Fred\\Model\\FredBlueprintCategory', 'Category');
        $c->leftJoin('Fred\\Model\\FredTheme', 'Theme', 'Category.theme = Theme.id');
        $c->leftJoin('Fred\\Model\\FredThemedTemplate', 'Templates', 'Templates.theme = Theme.id');

        $c->select($this->modx->getSelectColumns('Fred\\Model\\FredBlueprint', 'FredBlueprint', '', ['data'], true));
        $c->select($this->modx->getSelectColumns('Fred\\Model\\FredBlueprintCategory', 'Category', 'category_', ['name']));
        $c->select($this->modx->getSelectColumns('Fred\\Model\\FredTheme', 'Theme', 'theme_', ['name']));

        return parent::prepareQueryAfterCount($c);
    }
}
