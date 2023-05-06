<?php
defined('_JEXEC') or die;
jimport('joomla.application.component.modeladmin');

class RoombookingModelBooking extends JModelAdmin
{

	
	public function getTable($type = 'Booking', $prefix = 'Table', $config = array())
	{
		$table = JTable::getInstance($type, $prefix, $config);

		return $table;
	}

	
	public function getForm($data = array(), $loadData = true)
	{
		
		// Get the form.
		$form = $this->loadForm('com_roomname.booking', 'booking', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		
		return $form;
	}

	
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_roomname.edit.booking.data', array());
		if (empty($data)) {
			$data = $this->getItem();
		}
		return $data;
	}

	
	protected function preprocessForm(JForm $form, $data, $group = 'booking')
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
		//$holidays = $input->get('holidays' , array());
		$formData = new JRegistry($input->get('jform', '', 'array'));
		$booking_date = $formData->get('booking_date');
		$booking_id = $input->get('id');
		$room_id = $formData->get('room_id');
		$is_event = $formData->get('is_event');
		$extraquery = '';
		if($booking_id > 0) {
			$extraquery = " AND `id` != ".$booking_id ;
		}
		
		$checkin_time = $checkin_time_hour.'.'.$checkin_time_min;
		$checkout_time = $checkout_time_hour.'.'.$checkout_time_min;
		$data['checkin_time'] = $checkin_time;
		$data['checkout_time'] = $checkout_time; 
		//if(count($holidays) > 0)
//		{
//			$data['holidays'] = json_encode($holidays);
//		}
		$db->setQuery("SELECT * FROM `#__kirk_bookings` WHERE `booking_date` = '".$booking_date."' AND `room_id` = '".$room_id."' ".$extraquery);
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
			
			if($existing_booking_start_min >= $booking_start_min && $existing_booking_end_min <= $booking_end_min) { 
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
			if($booking_id > 0) {
				$regular_query = '';
				if($is_event == 0) {
					$regular_query = ' , `regular` = 0 ';
				}
				$db->setQuery("UPDATE `#__kirk_booking_events` SET `public` = '".$is_event."' ".$regular_query." WHERE `booking_id` = '".$booking_id."'");
				$db->Query();
				
				if($is_event == 0) {
					$db->setQuery("DELETE FROM `#__kirk_bookings` WHERE `booking_master_id` = '".$booking_id."'");
					$db->Query();
				}
				if(count($holidays) > 0)
				{
					foreach($holidays as $h)
					{
						$db->setQuery("DELETE FROM `#__kirk_bookings` WHERE `booking_date` = '".$h."' AND `room_id` = '".$room_id."' AND `booking_master_id` = '".$booking_id."'");
						$db->Query();
					}
				}
			}
			else {
				$new_booking_id = $db->insertid();
				$db->setQuery("INSERT INTO  `#__kirk_booking_events` VALUES ('' , '".$new_booking_id."' , 'Event Booked for Booking Id #".$new_booking_id."' , 'Event description for Booking Id #".$new_booking_id."' , 0 , 0 , '".$booking_date."' , 7 , '".$is_event."' , '')");
				$db->Query();
			}
			return true;
		}
	}

	
	

	
}
