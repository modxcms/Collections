<?php
namespace Collections\Processors\Resource;
use Collections\Model\CollectionTemplate;
use Collections\Model\CollectionTemplateColumn;
use Collections\Utils;
use MODX\Revolution\modResource;
use MODX\Revolution\Processors\Model\UpdateProcessor;
use MODX\Revolution\Validation\modValidator;

class UpdateFromGrid extends UpdateProcessor
{
    public $classKey = modResource::class;
    public $beforeSaveEvent = 'OnBeforeDocFormSave';
    public $afterSaveEvent = 'OnDocFormSave';
    public $objectType = 'resource';

    /** @var \Collections\Collections */
    protected $collections;

    public function initialize()
    {
        $data = $this->getProperty('data');
        if (empty($data)) return $this->modx->lexicon('invalid_data');
        $data = json_decode($data, true);
        if (empty($data)) return $this->modx->lexicon('invalid_data');
        $this->setProperties($data);
        $this->unsetProperty('data');

        $this->collections = $this->modx->services->get('collections');

        return parent::initialize();
    }

    public function process()
    {
        $this->unsetSnippetRendererColumns();

        /* Run the beforeSet method before setting the fields, and allow stoppage */
        $canSave = $this->beforeSet();
        if ($canSave !== true) {
            return $this->failure($canSave);
        }

        $this->object->fromArray($this->getProperties());

        /* Run the beforeSave method and allow stoppage */
        $canSave = $this->beforeSave();
        if ($canSave !== true) {
            return $this->failure($canSave);
        }

        /* run object validation */
        if (!$this->object->validate()) {
            /** @var modValidator $validator */
            $validator = $this->object->getValidator();
            if ($validator->hasMessages()) {
                foreach ($validator->getMessages() as $message) {
                    $this->addFieldError($message['field'], $this->modx->lexicon($message['message']));
                }
            }
        }

        /* run the before save event and allow stoppage */
        $preventSave = $this->fireBeforeSaveEvent();
        if (!empty($preventSave)) {
            return $this->failure($preventSave);
        }

        if ($this->saveObject() == false) {
            return $this->failure($this->modx->lexicon($this->objectType . '_err_save'));
        }

        $this->saveSpecialColumns();

        $this->afterSave();
        $this->fireAfterSaveEvent();
        $this->logManagerAction();
        return $this->cleanup();
    }

    private function unsetSnippetRendererColumns()
    {
        /** @var CollectionTemplate $view */
        $view = $this->collections->getCollectionsView($this->object->Parent);

        /** @var CollectionTemplateColumn[] $columns */
        $columns = $view->getMany('Columns', 'php_renderer != ""');

        foreach ($columns as $column) {
            unset($this->properties[$column->name]);
        }
    }

    public function saveSpecialColumns()
    {
        $fields = $this->getProperties();

        foreach ($fields as $key => $field) {
            if (strpos($key, 'tv_') !== false) {
                $this->saveTV(preg_replace('/tv_/', '', $key, 1), $field);
                continue;
            }

            $taggerInstalled = $this->collections->getOption('taggerInstalled', null, false);
            if ($taggerInstalled) {
                if (strpos($key, 'tagger_') !== false) {
                    $this->saveTagger(preg_replace('/tagger_/', '', $key, 1), $field);
                    continue;
                }
            }
        }
    }

    public function saveTV($key, $value)
    {
        $this->object->setTVValue($key, $value);
    }

    public function saveTagger($group, $tags)
    {
        $group = $this->modx->getObject('Tagger\\Model\\TaggerGroup', ['alias' => $group]);
        if (!$group) {
            return;
        }

        $showForTemplates = $group->show_for_templates;
        $showForTemplates = Utils::explodeAndClean($showForTemplates);
        $showForTemplates = array_flip($showForTemplates);

        if (!isset($showForTemplates[$this->object->template])) {
            return;
        }

        $oldTagsQuery = $this->modx->newQuery('Tagger\\Model\\TaggerTagResource');
        $oldTagsQuery->leftJoin('Tagger\\Model\\TaggerTag', 'Tag');
        $oldTagsQuery->where(['resource' => $this->object->id, 'Tag.group' => $group->id]);
        $oldTagsQuery->select($this->modx->getSelectColumns('Tagger\\Model\\TaggerTagResource', 'TaggerTagResource', '', ['tag']));

        $oldTagsQuery->prepare();
        $oldTagsQuery->stmt->execute();
        $oldTags = $oldTagsQuery->stmt->fetchAll(\PDO::FETCH_COLUMN, 0);
        $oldTags = array_flip($oldTags);

        $tags = Utils::explodeAndClean($tags);

        foreach ($tags as $tag) {
            /** @var \Tagger\Model\TaggerTag $tagObject */
            $tagObject = $this->modx->getObject('Tagger\\Model\\TaggerTag', ['tag' => $tag, 'group' => $group->id]);
            if ($tagObject) {
                $existsRelation = $this->modx->getObject('Tagger\\Model\\TaggerTagResource', ['tag' => $tagObject->id, 'resource' => $this->object->id]);
                if ($existsRelation) {
                    if (isset($oldTags[$existsRelation->tag])) {
                        unset($oldTags[$existsRelation->tag]);
                    }

                    continue;
                }
            }

            if (!$tagObject) {
                if (!$group->allow_new) {
                    continue;
                }

                $tagObject = $this->modx->newObject('Tagger\\Model\\TaggerTag');
                $tagObject->set('tag', $tag);
                $tagObject->addOne($group, 'Group');
                $tagObject->save();
            }

            /** @var \Tagger\Model\TaggerTagResource $relationObject */
            $relationObject = $this->modx->newObject('Tagger\\Model\\aggerTagResource');
            $relationObject->set('tag', $tagObject->id);
            $relationObject->set('resource', $this->object->id);
            $relationObject->save();
        }

        if (count($oldTags) > 0) {
            $oldTags = array_keys($oldTags);
            $this->modx->removeCollection('Tagger\\Model\\TaggerTagResource', [
                'tag:IN' => $oldTags,
                'AND:resource:=' => $this->object->id
            ]);
        }

        if ($group->remove_unused) {
            $c = $this->modx->newQuery('Tagger\\Model\\TaggerTagResource');
            $c->select($this->modx->getSelectColumns('Tagger\\Model\\TaggerTagResource', 'TaggerTagResource', '', ['tag']));
            $c->prepare();
            $c->stmt->execute();
            $IDs = $c->stmt->fetchAll(\PDO::FETCH_COLUMN, 0);

            $IDs = array_keys(array_flip($IDs));

            if (count($IDs) > 0) {
                $this->modx->removeCollection('Tagger\\Model\\TaggerTag', ['id:NOT IN' => $IDs, 'group' => $group->id]);
            }

        }
    }

}
