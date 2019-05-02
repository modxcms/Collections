<?php
namespace Collections\Events;

class FredOnBeforeGetResourceTree extends Event
{

    public function run()
    {
        $params = $this->modx->getOption('params', $this->scriptProperties);
        if (empty($params)) return false;

        $query = $this->modx->newQuery('modResource', [
            'class_key' => 'CollectionContainer'
        ]);
        
        $query->select($this->modx->getSelectColumns('modResource', 'modResource', '', ['id']));

        $query->prepare();
        $query->stmt->execute();
        
        while ($row = $query->stmt->fetch(\PDO::FETCH_ASSOC)) {
            $params->hideChildren[] = $row['id'];    
        }

        return true;
    }

}