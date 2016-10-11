<?php

/**
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property bool $global_template
 * @property bool $bulk_actions
 * @property bool $allow_dd
 * @property int $page_size
 * @property string $sort_field
 * @property string $sort_dir
 * @property string $sort_type
 * @property int $child_template
 * @property string $child_resource_type
 * @property bool $resource_type_selection
 * @property string $tab_label
 * @property string $button_label
 * @property string $content_place
 * @property int $view_for
 * @property string $link_label
 * @property string $context_menu
 * @property string $buttons
 * @property string $allowed_resource_types
 * @property string $back_to_collection_label
 * @property string $back_to_selection_label
 * @property string $selection_create_sort
 * @property bool|null $child_hide_from_menu
 * @property bool|null $child_published
 * @property bool|null $child_cacheable
 * @property bool|null $child_searchable
 * @property bool|null $child_richtext
 * @property int $child_content_type
 * @property string $parent
 * @property bool|null $child_content_disposition
 * @property string $permanent_sort_before
 * @property string $permanent_sort_after
 * @property string $selection_link_condition
 * @property bool|null $search_query_exclude_tvs
 * @property bool|null $search_query_exclude_tagger
 * @property bool|null $search_query_title_only
 *
 * @property CollectionSetting $Setting
 * @property CollectionTemplateColumn[] $Columns
 *
 * @package collections
 */
class CollectionTemplate extends xPDOSimpleObject
{
    public function setTemplates($templates)
    {
        $this->xpdo->removeCollection('CollectionResourceTemplate', array('collection_template' => $this->id));

        if (!empty($templates)) {
            foreach ($templates as $idTemplate) {
                $newTemplate = $this->xpdo->newObject('CollectionResourceTemplate');
                $newTemplate->set('collection_template', $this->id);
                $newTemplate->set('resource_template', $idTemplate);
                $newTemplate->save();
            }
        }

        return true;
    }

}
