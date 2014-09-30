<?php
/**
 * @package collections
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/collectionselection.class.php');
class CollectionSelection_mysql extends CollectionSelection {}
?>