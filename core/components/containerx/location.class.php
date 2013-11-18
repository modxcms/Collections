<?php
require_once dirname(__FILE__) . '/index.class.php';
/**
 * @package containerx
 */
class LocationManagerController extends ContainerXBaseManagerController {
    public static function getDefaultController() { return 'location/create'; }
}