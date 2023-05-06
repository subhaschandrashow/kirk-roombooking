<?php
defined('_JEXEC') or die();
jimport('joomla.application.component.controllerform');

class RoombookingControllerRoom extends JControllerForm
{
	protected	$option 		= 'com_roombooking';
	
	protected function allowAdd($data = array())
	{
		return parent::allowAdd($data);
	}

	protected function allowEdit($data = array(), $key = 'id')
	{
		$recordId	= (int) isset($data[$key]) ? $data[$key] : 0;
		$user		= JFactory::getUser();
		$userId		= $user->get('id');
 
		if ($user->authorise('core.edit', 'com_roombooking.room.'.$recordId)) {
			return true;
		}


		return parent::allowEdit($data, $key);
	}
	
	function delete($key = NULL, $urlVar = NULL) {
		$db = JFactory::getDBO();
		$cid = JRequest::getVar('cid');
		foreach($cid as $id) {
			$db->setQuery("DELETE FROM `#__kirk_rooms` WHERE `id` = '".$id."'");
			$db->Query();
		}
		
		$this->setRedirect('index.php?option=com_roombooking&view=rooms' , JText::_('DELETED'));
	}

 
}