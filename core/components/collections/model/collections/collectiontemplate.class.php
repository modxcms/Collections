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
 *
 * @property CollectionSetting $Setting
 * @property array $Columns
 *
 * @package collections
 */
class CollectionTemplate extends xPDOSimpleObject {
    public function setTemplates($templates) {
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
?>