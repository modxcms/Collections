<?php

namespace Collections\Events;

abstract class Event
{

    /** @var \modX $modx */
    protected $modx;

    /** @var \Collections\Collections $collections */
    protected $collections;

    /** @var array $scriptProperties */
    protected $scriptProperties;

    public function __construct($modx, &$scriptProperties)
    {
        $this->scriptProperties =& $scriptProperties;
        $this->modx = $modx;
        $this->collections = $this->modx->services->get('collections');
    }

    abstract public function run();
}
