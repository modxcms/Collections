<?php
/**
 * @package collections
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/selectioncontainer.class.php');
class SelectionContainer_mysql extends SelectionContainer {}
?>