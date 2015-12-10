<?php
/**
 * Create a Template
 *
 * @package collections
 * @subpackage processors.template
 */
class CollectionsTemplateImportProcessor extends modProcessor {

    public function process()
    {
        if (empty($_FILES['file'])) {
            return $this->failure();
        }
        
        $templates = file_get_contents($_FILES['file']['tmp_name']);

        $templates = $this->modx->fromJSON($templates);
        
        if (!is_array($templates)) {
            return $this->failure();
        }
        
        $importTemplates = $this->getProperty('template', null);
        
        foreach ($templates as $template) {
            if (!in_array($template['name'], $importTemplates) || $importTemplates === null) continue;
            
            $exists = $this->modx->getCount('CollectionTemplate', array('name' => $template['name']));
            if ($exists > 0) {
                $newName = $template['name'] . ' Imported';
                $exists = $this->modx->getCount('CollectionTemplate', array('name' => $newName));
                
                $i = 0;
                $testName = $newName;
                while ($exists > 0) {
                    $testName = $newName . $i;
                    $exists = $this->modx->getCount('CollectionTemplate', array('name' => $testName));
                    $i++;
                }
                
                $template['name'] = $testName;
            }
            
            /** @var CollectionTemplate $tplObject */
            $tplObject = $this->modx->newObject('CollectionTemplate');
            $tplObject->fromArray($template);
            
            $columns = array();
            foreach ($template['columns'] as $column) {
                /** @var CollectionTemplateColumn $colObject */
                $colObject = $this->modx->newObject('CollectionTemplateColumn');
                $colObject->fromArray($column);

                $columns[] = $colObject;
            }
            
            $tplObject->addMany($columns, 'Columns');
            $tplObject->save();
        }
        
        return $this->success();
    }
}
return 'CollectionsTemplateImportProcessor';