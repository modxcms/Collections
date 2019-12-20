<?php
namespace Collections\Processors\Template;

use Collections\Model\CollectionResourceTemplate;
use Collections\Model\CollectionTemplate;
use MODX\Revolution\Processors\Model\GetProcessor;

class Get extends GetProcessor
{
    public $classKey = CollectionTemplate::class;
    public $languageTopics = array('collections:default');
    public $objectType = 'collections.template';

    public function beforeOutput()
    {
        $c = $this->modx->newQuery(CollectionResourceTemplate::class);
        $c->where(array(
            'collection_template' => $this->object->id
        ));
        $c->select($this->modx->getSelectColumns(CollectionResourceTemplate::class, 'CollectionResourceTemplate', '', array('resource_template')));

        $c->prepare();
        $c->stmt->execute();
        $templates = $c->stmt->fetchAll(\PDO::FETCH_COLUMN, 0);

        $this->object->set('templates[]', $templates);

        return true;
    }

}
