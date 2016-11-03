<?php

/**
 * Update a Template
 *
 * @package collections
 * @subpackage processors.template
 */
class CollectionsTemplateUpdateProcessor extends modObjectUpdateProcessor
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
            if ($this->modx->getCount($this->classKey, array('name' => $name, 'id:!=' => $this->object->id)) > 0) {
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
        $this->handleComboBoolean('selection');
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

        $childTemplate = $this->getProperty('child_template', '');
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
        $templates = array_filter($templates, function ($var) {
            if ($var == '') {
                return false;
            }

            return true;
        });

        $c = $this->modx->newQuery('CollectionResourceTemplate');
        $c->leftJoin('modTemplate', 'ResourceTemplate');

        $where = array(
            'collection_template:!=' => $this->object->id
        );

        if (count($templates) > 0) {
            $where['resource_template:IN'] = $templates;

            $c->where($where);
            $c->select($this->modx->getSelectColumns('modTemplate', 'ResourceTemplate', '', array('templatename')));

            $c->prepare();
            $c->stmt->execute();
            $existingTemplates = $c->stmt->fetchAll(PDO::FETCH_COLUMN, 0);
            $existingTemplatesCount = count($existingTemplates);
            if ($existingTemplatesCount > 0) {
                $type = ($existingTemplatesCount > 1) ? 'p' : 's';
                return $this->modx->lexicon('collections.err.template_resource_template_aiu_' . $type, array('templates' => implode(',', $existingTemplates)));
            }
        }

        return parent::beforeSet();
    }

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

    public function afterSave()
    {
        $global = $this->getProperty('global_template');

        if ($global == true) {
            $this->modx->updateCollection('CollectionTemplate', array('global_template' => false), array('id:!=' => $this->object->id));
        }

        $templates = $this->getProperty('templates');
        $templates = array_filter($templates, function ($var) {
            if ($var == '') {
                return false;
            }

            return true;
        });

        $this->object->setTemplates($templates);

        return parent::afterSave();
    }

}

return 'CollectionsTemplateUpdateProcessor';
