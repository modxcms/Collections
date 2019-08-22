<?php
namespace Collections\Events;

class FredOnBeforeGetResourceTree extends Event
{
    /** @var \Fred */
    protected $fred;

    /** @var bool */
    protected $preventRun = false;

    public function __construct($modx, &$scriptProperties)
    {
        parent::__construct($modx, $scriptProperties);

        $corePath = $this->modx->getOption('fred.core_path', null, $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/fred/');

        $this->fred = $this->modx->getService(
            'fred',
            'Fred',
            $corePath . 'model/fred/',
            array(
                'core_path' => $corePath
            )
        );

        if ((!($this->fred instanceof \Fred)) || (version_compare(\Fred::VERSION, '1.1.0-pl') < 0)) $this->preventRun = true;
    }

    public function run()
    {
        if ($this->preventRun) return true;

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
