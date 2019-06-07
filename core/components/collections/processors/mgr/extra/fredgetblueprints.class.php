<?php

/**
 * Get list of Children
 *
 * @package collections
 * @subpackage processors.resource
 */
class CollectionsFredGetBlueprintsGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'FredBlueprint';
    public $defaultSortField = 'name';
    public $defaultSortDirection = 'asc';
    public $checkListPermission = true;
    public $languageTopics = array('fred:default');

    public function initialize()
    {
        $corePath = $this->modx->getOption('fred.core_path', null, $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/fred/');
        /** @var Fred $fred */
        $fred = $this->modx->getService(
            'fred',
            'Fred',
            $corePath . 'model/fred/',
            array(
                'core_path' => $corePath
            )
        );

        if (!($fred instanceof Fred)) {
            return false;
        }

        $this->setDefaultProperties(array(
            'start' => 0,
            'limit' => 20,
            'query' => '',
        ));

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
        $c->leftJoin('FredBlueprintCategory', 'Category');
        $c->leftJoin('FredTheme', 'Theme', 'Category.theme = Theme.id');
        $c->leftJoin('FredThemedTemplate', 'Templates', 'Templates.theme = Theme.id');

        $c->select($this->modx->getSelectColumns('FredBlueprint', 'FredBlueprint', '', ['data'], true));
        $c->select($this->modx->getSelectColumns('FredBlueprintCategory', 'Category', 'category_', ['name']));
        $c->select($this->modx->getSelectColumns('FredTheme', 'Theme', 'theme_', ['name']));

        return parent::prepareQueryAfterCount($c);
    }
}

return 'CollectionsFredGetBlueprintsGetListProcessor';
