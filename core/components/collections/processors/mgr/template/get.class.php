<?php
/**
 * Get Template
 *
 * @package collections
 * @subpackage processors
 */
class CollectionsTemplateGetProcessor extends modObjectGetProcessor {
    public $classKey = 'CollectionTemplate';
    public $languageTopics = array('collections:default');
    public $objectType = 'collections.template';

    public function beforeOutput() {
        $c = $this->modx->newQuery('CollectionResourceTemplate');
        $c->where(array(
           'collection_template' => $this->object->id
        ));
        $c->select($this->modx->getSelectColumns('CollectionResourceTemplate', 'CollectionResourceTemplate', '', array('resource_template')));

        $c->prepare();
        $c->stmt->execute();
        $templates = $c->stmt->fetchAll(PDO::FETCH_COLUMN, 0);

        $this->object->set('fake_templates', implode(',', $templates));

        return true;
    }

}
return 'CollectionsTemplateGetProcessor';