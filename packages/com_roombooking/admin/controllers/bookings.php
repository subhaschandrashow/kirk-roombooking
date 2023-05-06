<?php

defined('_JEXEC') or die;
use Joomla\Utilities\ArrayHelper;
jimport('joomla.application.component.controlleradmin');

 
class RoombookingControllerBookings extends JControllerAdmin
{
 
	public function getModel($name = 'Bookings', $prefix = 'RoombookingModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
	public function delete()
	{
		$app = JFactory::getApplication();
		$input = $app->input;
		$db    = JFactory::getDBO();
		$cids   = $input->get('cid');
		
		$db->setQuery("DELETE FROM `#__kirk_booking_events` WHERE `booking_id` IN (".implode(' , ' , $cids).")");
		$db->Query();
		
		$db->setQuery("DELETE FROM `#__kirk_bookings` WHERE `id` IN (".implode(' , ' , $cids).")");
		$db->Query();
		
		$db->setQuery("DELETE FROM `#__kirk_bookings` WHERE `booking_master_id` IN (".implode(' , ' , $cids).")");
		$db->Query();
		
		$this->setRedirect('index.php?option=com_roombooking&view=bookings' , JText::_('DELETED'));
	}
}
