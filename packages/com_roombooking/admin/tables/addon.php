<?php

defined('_JEXEC') or die('Restricted access');

jimport('joomla.filter.input');

class TableAddon extends JTable
{
	function __construct(& $db) {
		parent::__construct('#__kirk_addons', 'id', $db);
	}
}
?>