<?php

defined('_JEXEC') or die('Restricted access');

jimport('joomla.filter.input');

class TableBooking extends JTable
{
	function __construct(& $db) {
		parent::__construct('#__kirk_bookings', 'id', $db);
	}
}
?>