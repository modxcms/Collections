<?php

abstract class CollectionsPlugin
{

    /** @var \modX $modx */
    protected $modx;

    /** @var \Collections $collections */
    protected $collections;

    /** @var array $scriptProperties */
    protected $scriptProperties;

    public function __construct($modx, &$scriptProperties)
    {
        $this->scriptProperties =& $scriptProperties;
        $this->modx = $modx;
        $this->collections = $this->modx->collections;
    }

    abstract public function run();
}