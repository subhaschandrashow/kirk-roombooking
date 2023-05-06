<?php

defined('_JEXEC') or die;
use Joomla\Utilities\ArrayHelper;
jimport('joomla.application.component.controlleradmin');

 
class RoombookingControllerBooking_enquiries extends JControllerAdmin
{
 
	public function getModel($name = 'Booking_enquiries', $prefix = 'RoombookingModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
	
}
