<?php

namespace Collections\Events;

class FredBeforeRender extends Event
{

    public function run()
    {

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
