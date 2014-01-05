<?php
/**
 * Update menu index from a row update
 *
 * @package collections
 * @subpackage processors.resource
 */
class CollectionsUpdateFromGridProcessor extends modObjectUpdateProcessor {
    public $classKey = 'modResource';

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

        $this->object->set('menuindex', $this->getProperty('menuindex'));
        $this->object->set('template', $this->getProperty('template'));

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
        $this->afterSave();
        $this->fireAfterSaveEvent();
        $this->logManagerAction();
        return $this->cleanup();
    }

}
return 'CollectionsUpdateFromGridProcessor';