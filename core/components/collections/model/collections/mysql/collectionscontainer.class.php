<?php
/**
 * @package collections
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/collectionscontainer.class.php');
class CollectionsContainer_mysql extends CollectionsContainer {}
?>