<?php
namespace Collections\Model;

use xPDO\xPDO;

/**
 * Class CollectionTemplate
 *
 * @property string $name
 * @property string $description
 * @property boolean $global_template
 * @property boolean $bulk_actions
 * @property boolean $allow_dd
 * @property integer $page_size
 * @property string $sort_field
 * @property string $sort_dir
 * @property string $sort_type
 * @property integer $child_template
 * @property string $child_resource_type
 * @property boolean $resource_type_selection
 * @property string $tab_label
 * @property string $button_label
 * @property string $content_place
 * @property integer $view_for
 * @property string $link_label
 * @property string $context_menu
 * @property string $buttons
 * @property string $allowed_resource_types
 * @property string $back_to_collection_label
 * @property string $back_to_selection_label
 * @property string $selection_create_sort
 * @property boolean $child_hide_from_menu
 * @property boolean $child_published
 * @property boolean $child_cacheable
 * @property boolean $child_searchable
 * @property boolean $child_richtext
 * @property integer $child_content_type
 * @property string $parent
 * @property integer $child_content_disposition
 * @property string $permanent_sort_before
 * @property string $permanent_sort_after
 * @property string $selection_link_condition
 * @property boolean $search_query_exclude_tvs
 * @property boolean $search_query_exclude_tagger
 * @property boolean $search_query_title_only
 * @property boolean $show_quick_create
 * @property string $quick_create_label
 * @property string $fred_default_blueprint
 *
 * @property \Collections\Model\CollectionTemplateColumn[] $Columns
 * @property \Collections\Model\CollectionResourceTemplate $ResourceTemplates
 *
 * @package Collections\Model
 */
class CollectionTemplate extends \xPDO\Om\xPDOSimpleObject
{
    public function setTemplates($templates)
    {
        $this->xpdo->removeCollection(CollectionResourceTemplate::class, ['collection_template' => $this->id]);

        if (!empty($templates)) {
            foreach ($templates as $idTemplate) {
                $newTemplate = $this->xpdo->newObject(CollectionResourceTemplate::class);
                $newTemplate->set('collection_template', $this->id);
                $newTemplate->set('resource_template', $idTemplate);
                $newTemplate->save();
            }
        }

        return true;
    }
}
