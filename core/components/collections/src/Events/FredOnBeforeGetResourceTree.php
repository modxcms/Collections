<?php
namespace Collections\Events;

use Collections\Model\CollectionContainer;
use MODX\Revolution\modResource;

class FredOnBeforeGetResourceTree extends Event
{
    /** @var \Fred */
    protected $fred;

    /** @var bool */
    protected $preventRun = false;

    public function __construct($modx, &$scriptProperties)
    {
        parent::__construct($modx, $scriptProperties);

        if ($this->modx->services->has('fred')) {
            $this->fred = $this->modx->services->get('fred');
        } else {
            $this->preventRun = true;
        }
    }

    public function run()
    {
        if ($this->preventRun) return true;

        $params = $this->modx->getOption('params', $this->scriptProperties);
        if (empty($params)) return false;

        $query = $this->modx->newQuery(modResource::class, [
            'class_key' => CollectionContainer::class
        ]);

        $query->select($this->modx->getSelectColumns(modResource::class, 'modResource', '', ['id']));

        $query->prepare();
        $query->stmt->execute();

        while ($row = $query->stmt->fetch(\PDO::FETCH_ASSOC)) {
            $params->hideChildren[] = $row['id'];
        }

        return true;
    }

}
