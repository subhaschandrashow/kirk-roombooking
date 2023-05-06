<?php
defined('_JEXEC') or die;
jimport('joomla.application.component.modeladmin');

class RoombookingModelBooking_enquiry extends JModelAdmin
{

	
	public function getTable($type = 'Booking_enquiry', $prefix = 'Table', $config = array())
	{
		$table = JTable::getInstance($type, $prefix, $config);

		return $table;
	}

	
	public function getForm($data = array(), $loadData = true)
	{
		
		// Get the form.
		$form = $this->loadForm('com_roomname.booking_enquiry', 'booking_enquiry', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		
		return $form;
	}

	
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_roomname.edit.booking_enquiry.data', array());
		if (empty($data)) {
			$data = $this->getItem();
		}
		return $data;
	}

	
	protected function preprocessForm(JForm $form, $data, $group = 'booking_enquiry')
	{
		parent::preprocessForm($form, $data, $group);
	}

	
	public function save($data)
	{
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		$input = $app->input;
		$checkin_time_hour = $input->get('checkin_time_hour');
		$checkin_time_min = $input->get('checkin_time_min');
		$checkout_time_hour = $input->get('checkout_time_hour');
		$checkout_time_min = $input->get('checkout_time_min');
		$formData = new JRegistry($input->get('jform', '', 'array'));
		$booking_date = $formData->get('booking_date');
		$booking_id = $input->get('id');
		$room_id = $formData->get('room_id');
		$extraquery = '';
		if($booking_id > 0) {
			$extraquery = " AND `id` != ".$booking_id ;
		}
		
		$checkin_time = $checkin_time_hour.'.'.$checkin_time_min;
		$checkout_time = $checkout_time_hour.'.'.$checkout_time_min;
		$data['checkin_time'] = $checkin_time;
		$data['checkout_time'] = $checkout_time; 
		$db->setQuery("SELECT * FROM `#__kirk_booking_enquiries` WHERE `booking_date` = '".$booking_date."'  AND `room_id` = '".$room_id."' ".$extraquery);
		$result = $db->loadObjectList();
		$booking_start = explode('.' , $checkin_time) ;
		$booking_start_min = $booking_start[0] * 60 + $booking_start[1];
		$booking_out = explode('.' , $checkout_time) ;
		$booking_end_min = $booking_out[0] * 60 + $booking_out[1];
		if(count($result) > 0) {
		foreach($result as $r) {
			$existing_booking_start = explode('.' , $r->checkin_time) ;
			$existing_booking_start_min = $existing_booking_start[0] * 60 + $existing_booking_start[1];
			$existing_booking_out = explode('.' , $r->checkout_time) ;
			$existing_booking_end_min = $existing_booking_out[0] * 60 + $existing_booking_out[1];
			
			
			
			if($booking_start_min >= $existing_booking_start_min && $booking_start_min < $existing_booking_end_min) {
				$app->enqueueMessage(JText::_('Can\'t book in enquired timeslot'), 'error'); 
				return false;
			}
			
			if($booking_end_min >= $existing_booking_start_min && $booking_end_min <= $existing_booking_end_min) {
				$app->enqueueMessage(JText::_('Can\'t book in enquired timeslot'), 'error');
				return false;
			}
		}
		}
		
		$db->setQuery("SELECT * FROM `#__kirk_bookings` WHERE `booking_date` = '".$booking_date."'  AND `room_id` = '".$room_id."' ".$extraquery);
		$result = $db->loadObjectList();
		$booking_start = explode('.' , $checkin_time) ;
		$booking_start_min = $booking_start[0] * 60 + $booking_start[1];
		$booking_out = explode('.' , $checkout_time) ;
		$booking_end_min = $booking_out[0] * 60 + $booking_out[1];
		if(count($result) > 0) {
		foreach($result as $r) {
			$existing_booking_start = explode('.' , $r->checkin_time) ;
			$existing_booking_start_min = $existing_booking_start[0] * 60 + $existing_booking_start[1];
			$existing_booking_out = explode('.' , $r->checkout_time) ;
			$existing_booking_end_min = $existing_booking_out[0] * 60 + $existing_booking_out[1];
			
			
			
			if($booking_start_min >= $existing_booking_start_min && $booking_start_min < $existing_booking_end_min) {
				$app->enqueueMessage(JText::_('Can\'t book in booked timeslot'), 'error'); 
				return false;
			}
			
			if($booking_end_min >= $existing_booking_start_min && $booking_end_min <= $existing_booking_end_min) {
				$app->enqueueMessage(JText::_('Can\'t book in booked timeslot'), 'error');
				return false;
			}
		}
		}
		
		if($booking_end_min <= $booking_start_min) {
			$app->enqueueMessage(JText::_('End booking time cannot be less or equal to start booking time'), 'error');
			return false;
		}
		
		if (parent::save($data)) {
			return true;
		}
	}

	
	public function delete(&$pks)
	{
		$user  = JFactory::getUser();
		$table = $this->getTable();
		$pks   = (array) $pks;

		// Check if I am a Super Admin
		$iAmSuperAdmin = $user->authorise('core.admin');

		JPluginHelper::importPlugin($this->events_map['delete']);
		$dispatcher = JEventDispatcher::getInstance();

		if (in_array($user->id, $pks))
		{
			$this->setError(JText::_('COM_USERS_USERS_ERROR_CANNOT_DELETE_SELF'));

			return false;
		}

		// Iterate the items to delete each one.
		foreach ($pks as $i => $pk)
		{
			if ($table->load($pk))
			{
				// Access checks.
				$allow = $user->authorise('core.delete', 'com_users');

				// Don't allow non-super-admin to delete a super admin
				$allow = (!$iAmSuperAdmin && JAccess::check($pk, 'core.admin')) ? false : $allow;

				if ($allow)
				{
					// Get users data for the users to delete.
					$user_to_delete = JFactory::getUser($pk);

					// Fire the before delete event.
					$dispatcher->trigger($this->event_before_delete, array($table->getProperties()));

					if (!$table->delete($pk))
					{
						$this->setError($table->getError());

						return false;
					}
					else
					{
						// Trigger the after delete event.
						$dispatcher->trigger($this->event_after_delete, array($user_to_delete->getProperties(), true, $this->getError()));
					}
				}
				else
				{
					// Prune items that you can't change.
					unset($pks[$i]);
					JError::raiseWarning(403, JText::_('JERROR_CORE_DELETE_NOT_PERMITTED'));
				}
			}
			else
			{
				$this->setError($table->getError());

				return false;
			}
		}

		return true;
	}

	
}
