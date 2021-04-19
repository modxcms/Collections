<?php
namespace Collections\Processors\Template\Column;

use Collections\Model\CollectionTemplateColumn;
use MODX\Revolution\modTemplateVar;
use MODX\Revolution\Processors\Model\UpdateProcessor;

class Update extends UpdateProcessor
{
    public $classKey = CollectionTemplateColumn::class;
    public $languageTopics = array('collections:default');
    public $objectType = 'collections.template.column';
    /** @var CollectionTemplateColumn $object */
    public $object;

    /** @var \Collections\Collections */
    protected $collections;

    public function initialize()
    {
        $this->collections = $this->modx->services->get('collections');

        return parent::initialize();
    }


    public function beforeSet()
    {
        $template = (int)$this->getProperty('template');

        if ($template <= 0) return false;

        $name = $this->getProperty('name');

        if (empty($name)) {
            $this->addFieldError('name', $this->modx->lexicon('collections.err.column_ns_name'));
        } else {
            if (strpos($name, '.') !== false) {
                $this->addFieldError('name', $this->modx->lexicon('collections.err.column_dot_name'));
            }

            if ($this->modx->getCount($this->classKey, array('name' => $name, 'template' => $template, 'id:!=' => $this->object->id)) > 0) {
                $this->addFieldError('name', $this->modx->lexicon('collections.err.column_ae_name'));
            }
        }

        $label = $this->getProperty('label');
        if (empty($label)) {
            $autoLabel = false;

            if (strpos($name, 'tv_') !== false) {
                $tvName = preg_replace('/tv_/', '', $name, 1);
                /** @var modTemplateVar $tv */
                $tv = $this->modx->getObject(modTemplateVar::class, array('name' => $tvName));
                if ($tv) {
                    $this->setProperty('label', $tv->caption);
                    $autoLabel = true;
                }
            }

            $useTagger = $this->collections->getOption('taggerInstalled', null, false);
            if ($useTagger && (strpos($name, 'tagger_') !== false)) {
                $groupName = preg_replace('/tagger_/', '', $name, 1);
                /** @var \Tagger\Model\TaggerGroup $taggerGroup */
                $taggerGroup = $this->modx->getObject('Tagger\\Model\\TaggerGroup', array('alias' => $groupName));
                if ($taggerGroup) {
                    $this->setProperty('label', $taggerGroup->name);
                    $autoLabel = true;
                }
            }

            if (!$autoLabel) {
                $this->addFieldError('label', $this->modx->lexicon('collections.err.template_ns_label'));
            }
        }

        if ($this->object->name == 'id' && $name != 'id') {
            $this->addFieldError('name', $this->modx->lexicon('collections.err.column_name_cant_change'));
        }

        $position = $this->getProperty('position', '');
        if ($position === '') {
            $c = $this->modx->newQuery(CollectionTemplateColumn::class);
            $c->where(array(
                'template' => $template,
                'id:!=' => $this->object->id
            ));
            $c->limit(1);
            $c->sortby('position', 'DESC');

            $last = 0;

            $columns = $this->modx->getIterator(CollectionTemplateColumn::class, $c);
            foreach ($columns as $column) {
                $last = $column->position + 1;
                break;
            }

            $this->setProperty('position', $last);
        } else {
            $c = $this->modx->newQuery(CollectionTemplateColumn::class);
            $c->where(array(
                'template' => $template,
                'position:>=' => $position,
                'id:!=' => $this->object->id
            ));
            $c->sortby('position', 'ASC');

            /** @var CollectionTemplateColumn[] $columns */
            $columns = $this->modx->getIterator(CollectionTemplateColumn::class, $c);
            $tmpPosition = $position;
            foreach ($columns as $column) {
                if ($tmpPosition == $column->position) {
                    $tmpPosition = $column->position + 1;
                    $column->set('position', $tmpPosition);
                    $column->save();
                } else {
                    break;
                }
            }
        }

        $this->handleNull('sort_type');

        return parent::beforeSet();
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

        return parent::afterSave();
    }

}
