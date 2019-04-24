<?php
namespace Collections\Events;

class OnManagerPageInit extends Event
{

    public function run()
    {
        $cssFile = $this->collections->getOption('assetsUrl') . 'css/mgr.css';
        $this->modx->regClientCSS($cssFile);
    }
}