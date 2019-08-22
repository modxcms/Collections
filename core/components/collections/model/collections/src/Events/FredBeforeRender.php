<?php

namespace Collections\Events;

class FredBeforeRender extends Event
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

        $assetsPath = $this->collections->getOption('assets_url', [], $this->modx->getOption('assets_url', null, MODX_ASSETS_URL) . 'components/collections/');

        $assetsPath = rtrim($assetsPath, '/') . '/web/';

        $includes = '
            <script type="text/javascript" src="' . $assetsPath . 'fred_integration.js"></script>
            <link rel="stylesheet" type="text/css" href="' . $assetsPath . 'fred_integration.css">
            <link rel="stylesheet" type="text/css" href="' . $assetsPath . 'tabulator_bulma.css">
        ';

        $endpoint = $assetsPath . 'ajax.php';

        $beforeRender = '
            this.registerSidebarPlugin("CollectionsSidebar", FredCollections.SidebarPlugin({endpoint:"' . $endpoint . '"}));
        ';

        $this->modx->event->_output = [
            'includes' => $includes,
            'beforeRender' => $beforeRender,
            'lexicons' => ['collections:fred'],
        ];

        return true;
    }

}
