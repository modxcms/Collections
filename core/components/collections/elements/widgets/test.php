<?php

use MODX\Revolution\modDashboardWidgetInterface;

/**
 * @package modx
 * @subpackage dashboard
 */
class TestWidget extends modDashboardWidgetInterface
{
    /**
     * @return string
     * @throws Exception
     */
    public function render()
    {
        return "<div><strong>Test</strong></div>";
    }


    /**
     * @return string
     * @throws Exception
     */
    public function process()
    {
        return "<div><strong>Test2</strong></div>";
    }
}

return 'TestWidget';
