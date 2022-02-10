<?php
namespace Collections\Processors\Template;

use Collections\Model\CollectionTemplate;
use Collections\Model\CollectionTemplateColumn;
use MODX\Revolution\Processors\Processor;

class Import extends Processor
{

    public function process()
    {
        if (empty($_FILES['file'])) {
            return $this->failure();
        }

        $templates = file_get_contents($_FILES['file']['tmp_name']);

        $templates = json_decode($templates, true);

        if (!is_array($templates)) {
            return $this->failure();
        }

        $importTemplates = $this->getProperty('template', null);

        foreach ($templates as $template) {
            if (!in_array($template['name'], $importTemplates) || $importTemplates === null) continue;

            $exists = $this->modx->getCount(CollectionTemplate::class, ['name' => $template['name']]);
            if ($exists > 0) {
                $newName = $template['name'] . ' Imported';
                $exists = $this->modx->getCount(CollectionTemplate::class, ['name' => $newName]);

                $i = 0;
                $testName = $newName;
                while ($exists > 0) {
                    $testName = $newName . $i;
                    $exists = $this->modx->getCount(CollectionTemplate::class, ['name' => $testName]);
                    $i++;
                }

                $template['name'] = $testName;
            }

            /** @var CollectionTemplate $tplObject */
            $tplObject = $this->modx->newObject(CollectionTemplate::class);
            $tplObject->fromArray($template);

            $columns = array();
            foreach ($template['columns'] as $column) {
                /** @var CollectionTemplateColumn $colObject */
                $colObject = $this->modx->newObject(CollectionTemplateColumn::class);
                $colObject->fromArray($column);

                $columns[] = $colObject;
            }

            $tplObject->addMany($columns, 'Columns');
            $tplObject->save();
        }

        return $this->success();
    }
}
