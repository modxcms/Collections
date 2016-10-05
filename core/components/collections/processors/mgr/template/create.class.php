<?php

/**
 * Create a Template
 *
 * @package collections
 * @subpackage processors.template
 */
class CollectionsTemplateCreateProcessor extends modObjectCreateProcessor
{
    public $classKey = 'CollectionTemplate';
    public $languageTopics = array('collections:default');
    public $objectType = 'collections.template';
    /** @var CollectionTemplate $object */
    public $object;

    public function beforeSet()
    {
        $name = $this->getProperty('name');
        if (empty($name)) {
            $this->addFieldError('name', $this->modx->lexicon('collections.err.template_ns_name'));
        } else {
            if ($this->doesAlreadyExist(array('name' => $name))) {
                $this->addFieldError('name', $this->modx->lexicon('collections.err.template_ae_name'));
            }
        }

        $global = $this->handleComboBoolean('global_template');
        if ($global == false) {
            $templatesCount = $this->modx->getCount('CollectionTemplate', array('global_template' => 1, 'id:!=' => $this->object->id));
            if ($templatesCount == 0) {
                $this->setProperty('global_template', true);
            }
        }

        $this->handleComboBoolean('bulk_actions');
        $this->handleComboBoolean('allow_dd');
        $this->handleComboBoolean('resource_type_selection');
        $this->handleComboBoolean('child_hide_from_menu');
        $this->handleComboBoolean('child_published');
        $this->handleComboBoolean('child_cacheable');
        $this->handleComboBoolean('child_searchable');
        $this->handleComboBoolean('child_richtext');
        $this->handleComboBoolean('search_query_exclude_tvs');
        $this->handleComboBoolean('search_query_exclude_tagger');
        $this->handleComboBoolean('search_query_title_only');
        
        $this->handleNull('child_content_disposition');
        $this->handleNull('sort_type');

        $childTemplate = $this->getProperty('child_template');
        if ($childTemplate == '') {
            $this->setProperty('child_template', null);
        }

        $tabLabel = $this->getProperty('tab_label');
        if (empty($tabLabel)) {
            $this->setProperty('tab_label', 'collections.children');
        }

        $buttonLabel = $this->getProperty('button_label');
        if (empty($buttonLabel)) {
            $this->setProperty('button_label', 'collections.children.create');
        }

        $linkLabel = $this->getProperty('link_label');
        if (empty($linkLabel)) {
            $this->setProperty('link_label', 'selections.create');
        }

        $context_menu = $this->getProperty('context_menu');
        if (empty($context_menu)) {
            $this->setProperty('context_menu', 'view,edit,duplicate,publish,unpublish,-,delete,undelete,remove,-,unlink');
        }

        $buttons = $this->getProperty('buttons');
        if (empty($buttons)) {
            $this->setProperty('buttons', 'open,view,edit,duplicate,publish:orange,unpublish,delete,undelete,remove,unlink');
        }

        $backToCollection = $this->getProperty('back_to_collection_label');
        if (empty($backToCollection)) {
            $this->setProperty('back_to_collection_label', 'collections.children.back_to_collection_label');
        }

        $backToSelection = $this->getProperty('back_to_selection_label');
        if (empty($backToSelection)) {
            $this->setProperty('back_to_selection_label', 'selections.back_to_selection_label');
        }

        $selectionCreateSort = $this->getProperty('selection_create_sort');
        if (empty($selectionCreateSort)) {
            $this->setProperty('selection_create_sort', 'id:desc');
        }

        $templates = $this->getProperty('templates');
        $templates = $this->modx->collections->explodeAndClean($templates);

        if (count($templates) > 0) {
            $validateTemplates = $this->validateTemplates($templates);
            if ($validateTemplates !== true) {
                return $validateTemplates;
            }
        }

        $selection = $this->handleComboBoolean('selection');
        if ($selection == true) {
            $switched = $this->handleSelectionSwitch($global, $templates);

            if ($switched !== true) {
                return $switched;
            }
        }

        return parent::beforeSet();
    }

    /**
     * Transforms string true/false value to boolean
     *
     * @param string $property
     * @return bool|null
     */
    public function handleComboBoolean($property)
    {
        $boolean = $this->getProperty($property);

        if ($boolean == 'true') {
            $this->setProperty($property, true);

            return true;
        }

        if ($boolean == 'false') {
            $this->setProperty($property, false);

            return false;
        }

        $this->setProperty($property, null);

        return null;
    }

    public function handleNull($property)
    {
        $value = $this->getProperty($property);

        if ($value == '') {
            $this->setProperty($property, null);

            return null;
        }

        $this->setProperty($property, $value);

        return $value;
    }

    /**
     * Validates if given templates are not used in other View
     *
     * @param int[] $templates
     * @return bool|string
     */
    public function validateTemplates($templates)
    {
        $c = $this->modx->newQuery('CollectionResourceTemplate');
        $c->leftJoin('modTemplate', 'ResourceTemplate');
        $c->where(array(
            'resource_template:IN' => $templates,
        ));
        $c->select($this->modx->getSelectColumns('modTemplate', 'ResourceTemplate', '', array('templatename')));

        $c->prepare();
        $c->stmt->execute();
        $existingTemplates = $c->stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        $existingTemplatesCount = count($existingTemplates);

        if ($existingTemplatesCount > 0) {
            $type = ($existingTemplatesCount > 1) ? 'p' : 's';
            return $this->modx->lexicon('collections.err.template_resource_template_aiu_' . $type, array('templates' => implode(', ', $existingTemplates)));
        }

        return true;
    }

    /**
     * Handles switch to Selection when Global view is set
     * or when there are templates specified
     *
     * @param bool $global
     * @param int[] $templates
     * @return bool|string
     */
    public function handleSelectionSwitch($global, $templates)
    {
        if (($global == false) && empty($templates)) return true;

        if ($global == true) {
            $globalSwitch = $this->handleSelectionSwitchForGlobalView();
            if ($globalSwitch !== true) {
                return $globalSwitch;
            }
        }

        if (!empty($templates)) {
            $templatesSwitch = $this->handleSelectionSwitchForTemplates($templates);
            if ($templatesSwitch !== true) {
                return $templatesSwitch;
            }
        }

        return true;
    }

    /**
     * When View is set as global,
     * will check if there are any templates (unassigned to other view)
     * that have Collections with children
     *
     * @return bool|string
     */
    public function handleSelectionSwitchForGlobalView()
    {
        //@TODO: Look for CollectionSetting if there is not an override for View
        //@TODO: Look for CollectionSetting if there is not an override for Selection
        $templatesQuery = $this->modx->newQuery('CollectionResourceTemplate');
        $templatesQuery->leftJoin('CollectionTemplate', 'CollectionTemplate');

        $templatesQuery->where(array(
            'CollectionTemplate.selection' => 0,
        ));

        $templatesQuery->select($this->modx->getSelectColumns('CollectionResourceTemplate', 'CollectionResourceTemplate', '', array('resource_template')));

        $templatesQuery->prepare();
        $templatesQuery->stmt->execute();

        $templates = $templatesQuery->stmt->fetchAll(PDO::FETCH_COLUMN, 0);

        $resourcesQuery = $this->modx->newQuery('modResource');
        $resourcesQuery->where(array(
            'class_key' => 'CollectionContainer'
        ));

        if (!empty($templates)) {
            $resourcesQuery->where(array(
                'template:NOT IN' => $templates
            ));
        }

        /** @var modResource[] $resources */
        $resources = $this->modx->getIterator('modResource', $resourcesQuery);

        return $this->checkResourcesForChildren($resources);
    }

    /**
     * Check if any of given Resources has children
     *
     * @param modResource[] $resources
     * @return bool|string
     */
    public function checkResourcesForChildren(array $resources)
    {
        $withChildren = array();

        foreach ($resources as $resource) {
            if ($resource->hasChildren()) $withChildren[] = '- ' . $resource->pagetitle . ' (' . $resource->id . ')';
        }

        if (!empty($withChildren)) {
            $type = (count($withChildren) > 1) ? 's' : '';
            return $this->modx->lexicon('collection.err.selection_resource' . $type . '_children', array('resources' => implode('<br />', $withChildren)));
        }

        return true;
    }

    /**
     * When templates are assigned to View,
     * will check if there are any Collections using those templates
     * and having children
     *
     * @param int[] $templates
     * @return bool|string
     */
    public function handleSelectionSwitchForTemplates($templates)
    {
        //@TODO: Look for CollectionSetting if there is not an override for View
        //@TODO: Look for CollectionSetting if there is not an override for Selection
        $resourcesQuery = $this->modx->newQuery('modResource');
        $resourcesQuery->where(array(
            'class_key' => 'CollectionContainer'
        ));

        if (!empty($templates)) {
            $resourcesQuery->where(array(
                'template:IN' => $templates
            ));
        }

        /** @var modResource[] $resources */
        $resources = $this->modx->getIterator('modResource', $resourcesQuery);

        return $this->checkResourcesForChildren($resources);
    }

    public function afterSave()
    {
        $global = $this->getProperty('global_template');

        if ($global == true) {
            $this->modx->updateCollection('CollectionTemplate', array('global_template' => false), array('id:!=' => $this->object->id));
        }

        $templates = $this->getProperty('templates');
        $templates = $this->modx->collections->explodeAndClean($templates);

        $this->object->setTemplates($templates);

        $this->addIdColumn();

        return parent::afterSave();
    }

    /**
     * Adds an ID column to the view
     */
    public function addIdColumn()
    {
        $column = $this->modx->newObject('CollectionTemplateColumn');
        $column->set('name', 'id');
        $column->set('label', 'id');
        $column->set('hidden', true);
        $column->set('width', 40);
        $column->set('template', $this->object->id);
        $column->save();
    }

}

return 'CollectionsTemplateCreateProcessor';
