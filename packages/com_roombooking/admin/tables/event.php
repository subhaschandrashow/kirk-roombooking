<?php

defined('_JEXEC') or die('Restricted access');

jimport('joomla.filter.input');

class TableEvent extends JTable
{
	function __construct(& $db) {
		parent::__construct('#__kirk_booking_events', 'id', $db);
	}
}
?>