<?php
/**
 * Update menu index from a row update
 *
 * @package collections
 * @subpackage processors.resource
 */
class CollectionsSelectionUpdateFromGridProcessor extends modObjectUpdateProcessor {
    public $classKey = 'modResource';
    public $beforeSaveEvent = 'OnBeforeDocFormSave';
    public $afterSaveEvent = 'OnDocFormSave';
    public $objectType = 'resource';

    public function initialize() {
        $data = $this->getProperty('data');
        if (empty($data)) return $this->modx->lexicon('invalid_data');
        $data = $this->modx->fromJSON($data);
        if (empty($data)) return $this->modx->lexicon('invalid_data');
        $this->setProperties($data);
        $this->unsetProperty('data');

        return parent::initialize();
    }

    public function process() {
        /* Run the beforeSet method before setting the fields, and allow stoppage */
        $canSave = $this->beforeSet();
        if ($canSave !== true) {
            return $this->failure($canSave);
        }

        $selection = $this->modx->getObject('CollectionSelection', array('resource' => $this->getProperty('id'), 'collection' => $this->getProperty('collection')));
        if ($selection) {
            $selection->set('menuindex', $this->getProperty('menuindex'));
            $selection->save();
        }

        $properties = $this->getProperties();
        unset($properties['menuindex']);

        $this->object->fromArray($properties);

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
                    $this->addFieldError($message['field'],$this->modx->lexicon($message['message']));
                }
            }
        }

        /* run the before save event and allow stoppage */
        $preventSave = $this->fireBeforeSaveEvent();
        if (!empty($preventSave)) {
            return $this->failure($preventSave);
        }

        if ($this->saveObject() == false) {
            return $this->failure($this->modx->lexicon($this->objectType.'_err_save'));
        }

        $this->saveSpecialColumns();

        $this->afterSave();
        $this->fireAfterSaveEvent();
        $this->logManagerAction();
        return $this->cleanup();
    }

    public function saveSpecialColumns(){
        $fields = $this->getProperties();

        foreach ($fields as $key => $field) {
            if (strpos($key, 'tv_') !== false) {
                $this->saveTV(preg_replace('/tv_/', '', $key, 1), $field);
                continue;
            }

            $taggerInstalled = $this->modx->collections->getOption('taggerInstalled', null,  false);
            if ($taggerInstalled) {
                if (strpos($key, 'tagger_') !== false) {
                    $this->saveTagger(preg_replace('/tagger_/', '', $key, 1), $field);
                    continue;
                }
            }
        }
    }

    public function saveTV($key, $value) {
        $this->object->setTVValue($key, $value);
    }

    public function saveTagger($group, $tags) {
        $group = $this->modx->getObject('TaggerGroup', array('alias' => $group));
        if (!$group) {
            return;
        }

        $showForTemplates = $group->show_for_templates;
        $showForTemplates = $this->modx->collections->explodeAndClean($showForTemplates);
        $showForTemplates = array_flip($showForTemplates);

        if (!isset($showForTemplates[$this->object->template])) {
            return;
        }

        $oldTagsQuery = $this->modx->newQuery('TaggerTagResource');
        $oldTagsQuery->leftJoin('TaggerTag', 'Tag');
        $oldTagsQuery->where(array('resource' => $this->object->id, 'Tag.group' => $group->id));
        $oldTagsQuery->select($this->modx->getSelectColumns('TaggerTagResource', 'TaggerTagResource', '', array('tag')));

        $oldTagsQuery->prepare();
        $oldTagsQuery->stmt->execute();
        $oldTags = $oldTagsQuery->stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        $oldTags = array_flip($oldTags);

        $tags = $this->modx->collections->explodeAndClean($tags);

        foreach ($tags as $tag) {
            /** @var TaggerTag $tagObject */
            $tagObject = $this->modx->getObject('TaggerTag', array('tag' => $tag, 'group' => $group->id));
            if ($tagObject) {
                $existsRelation = $this->modx->getObject('TaggerTagResource', array('tag' => $tagObject->id, 'resource' => $this->object->id));
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

                $tagObject = $this->modx->newObject('TaggerTag');
                $tagObject->set('tag', $tag);
                $tagObject->addOne($group, 'Group');
                $tagObject->save();
            }

            /** @var TaggerTagResource $relationObject */
            $relationObject = $this->modx->newObject('TaggerTagResource');
            $relationObject->set('tag', $tagObject->id);
            $relationObject->set('resource', $this->object->id);
            $relationObject->save();
        }

        if (count($oldTags) > 0) {
            $oldTags = array_keys($oldTags);
            $this->modx->removeCollection('TaggerTagResource', array(
                'tag:IN' => $oldTags,
                'AND:resource:=' => $this->object->id
            ));
        }

        if ($group->remove_unused) {
            $c = $this->modx->newQuery('TaggerTagResource');
            $c->select($this->modx->getSelectColumns('TaggerTagResource', 'TaggerTagResource', '', array('tag')));
            $c->prepare();
            $c->stmt->execute();
            $IDs = $c->stmt->fetchAll(PDO::FETCH_COLUMN, 0);

            $IDs = array_keys(array_flip($IDs));

            if (count($IDs) > 0) {
                $this->modx->removeCollection('TaggerTag', array('id:NOT IN' => $IDs, 'group' => $group->id));
            }

        }
    }

}
return 'CollectionsSelectionUpdateFromGridProcessor';