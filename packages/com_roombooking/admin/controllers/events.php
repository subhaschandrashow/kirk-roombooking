<?php

defined('_JEXEC') or die;
use Joomla\Utilities\ArrayHelper;
jimport('joomla.application.component.controlleradmin');

 
class RoombookingControllerEvents extends JControllerAdmin
{
 
	public function getModel($name = 'Events', $prefix = 'RoombookingModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
	public function publish()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$db = JFactory::getDBO();
		$ids    = $this->input->get('cid', array(), 'array');
		$values = array('status' => 1, 'status' => 0);
		$task   = $this->getTask();
		$value  = ArrayHelper::getValue($values, $task, 0, 'int');
		
		if (empty($ids))
		{
			JError::raiseWarning(500, JText::_('COM_BANNERS_NO_BANNERS_SELECTED'));
		}
		else
		{  
			foreach($ids as $id) {
				if($task == 'unpublish') {
					$db->setQuery("DELETE FROM `#__kirk_booking_events` WHERE `event_id` = '".$id."'");
				}
				else {
					$db->setQuery("INSERT INTO `#__kirk_booking_events` VALUES ('' , '".$id."')");
				}
				$db->Query();
			}
			
		}
		$this->setRedirect('index.php?option=com_roombooking&view=events' , 'Status Changed');
	}
}
