<?php

defined('_JEXEC') or die;
use Joomla\Utilities\ArrayHelper;
jimport('joomla.application.component.controlleradmin');

 
class RoombookingControllerRooms extends JControllerAdmin
{
 
	public function getModel($name = 'Rooms', $prefix = 'RoombookingModel', $config = array('ignore_request' => true))
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
			if($task == 'unpublish') {
				$db->setQuery("UPDATE `#__kirk_rooms` SET `status` = 0 WHERE `id` IN (".implode(',' , $ids).")");
			}
			else {
				$db->setQuery("UPDATE `#__kirk_rooms` SET `status` = 1 WHERE `id` IN (".implode(',' , $ids).")");
			}
			$db->Query();
		}

		$this->setRedirect('index.php?option=com_roombooking&view=rooms');
	}
}
