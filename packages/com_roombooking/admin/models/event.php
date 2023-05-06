<?php
defined('_JEXEC') or die;
jimport('joomla.application.component.modeladmin');

class RoombookingModelEvent extends JModelAdmin
{


	public function getTable($type = 'Event', $prefix = 'Table', $config = array())
	{
		$table = JTable::getInstance($type, $prefix, $config);

		return $table;
	}

	public function getForm($data = array(), $loadData = true)
	{

		// Get the form.
		$form = $this->loadForm('com_roomname.event', 'event', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}


		return $form;
	}


	protected function loadFormData()
	{

		$data = $this->getItem();
		return $data;
	}

	public function getItem($pk = null) {
		$app = JFactory::getApplication();
		$input = $app->input;
		$id = $input->get('id');
		$db = JFactory::getDBO();
		if($id > 0) {
			$db->setQuery("SELECT a.* , b.room_id , b.booking_date , b.checkin_time , b.checkout_time , b.customer_name , b.customer_phone , b.customer_email , b.customer_address , b.booking_reason , b.admin_note FROM `#__kirk_booking_events` as a , `#__kirk_bookings` as b WHERE a.booking_id = b.id AND a.id=".$id);
			$item = $db->loadObject();
		}
		else {
        	$item = parent::getItem($pk);
			$item->event_enddate = date('Y-m-d' , strtotime('+ 365 days'));
		}

        return $item;
      }


	protected function preprocessForm(JForm $form, $data, $group = 'event')
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
		$booking_id = $input->get('booking_id');
		$event_id = $input->get('id');
		$room_id = $formData->get('room_id');
		$extraquery = '';
		if($booking_id > 0) {
			$extraquery = " AND `id` != ".$booking_id ;
		}
		$holiday_start = $input->get('holiday_start' , array());
		$holiday_end = $input->get('holiday_end' , array());
		$holidays = array();
		$hcount = count($holiday_start);
		for($i = 0; $i < $hcount; $i++)
		{
			$holidays[] = $holiday_start[$i].'***'.$holiday_end[$i];
		}
		$holidays_json = '';
		$holidays_json = json_encode($holidays);

		$checkin_time = $checkin_time_hour.'.'.$checkin_time_min;
		$checkout_time = $checkout_time_hour.'.'.$checkout_time_min;
		$data['checkin_time'] = $checkin_time;
		$data['checkout_time'] = $checkout_time;
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

				if($booking_end_min > $existing_booking_start_min && $booking_end_min <= $existing_booking_end_min) {
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

		if($data['regular'] == 1) {
			$period_dates = array();
			$event_period = $data['event_period'];
			$event_enddate = $data['event_enddate'];
			$begin = new DateTime( $booking_date );
			$end = new DateTime( $event_enddate );
			$end = $end->modify( '+1 day' );

			if($event_period == 0) {
				$interval = new DateInterval('P1D');
			}
			elseif($event_period == 7) {
				$interval = new DateInterval('P7D');
			}
			elseif($event_period == 15) {
				$interval = new DateInterval('P14D');
			}
			elseif($event_period == 30) {
				$interval = new DateInterval('P1M');
			}
			elseif($event_period == 365) {
				$interval = new DateInterval('P1Y');
			}

			$daterange = new DatePeriod($begin, $interval ,$end);

			foreach($daterange as $date){
				$period_dates[] = $date->format("Y-m-d") ;
			}

			if(count($holidays) > 0)
			{
				foreach($holidays as $h)
				{
					$holiday_dates = explode('***' , $h);
					$start_date = $holiday_dates[0];
					$end_date = $holiday_dates[1];
					$interval = new DateInterval('P1D');
					$begin = new DateTime( $start_date );
					$end = new DateTime( $end_date );
					$holidayranges = new DatePeriod($begin, $interval ,$end);

					foreach($holidayranges as $hdate)
					{   $tdate = $hdate->format("Y-m-d") ;
						if($tdate != $booking_date)
						{
							if (($key = array_search($tdate, $period_dates)) !== false) {
								unset($period_dates[$key]);
							}
						}
					}
				}
			}

			foreach($period_dates as $pdate) {
				if($pdate != $booking_date) {
					$extraquery = '';
					if($booking_id > 0) {
						$extraquery = " AND `booking_master_id` != ".$booking_id ;
					}

					$db->setQuery("SELECT * FROM `#__kirk_bookings` WHERE `booking_date` = '".$pdate."' AND `room_id` = '".$room_id."' ".$extraquery);
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



						if($booking_start_min >= $existing_booking_start_min && $booking_start_min <= $existing_booking_end_min) {
							$app->enqueueMessage(JText::_('Can\'t book in booked timeslot (for periodic bookings) .. Date: '.$pdate), 'error');
							return false;
						}

						if($booking_end_min > $existing_booking_start_min && $booking_end_min <= $existing_booking_end_min) {
							$app->enqueueMessage(JText::_('Can\'t book in booked timeslot (for periodic bookings) .. Date: '.$pdate), 'error');
							return false;
						}

						if($existing_booking_start_min >= $booking_start_min && $existing_booking_end_min <= $booking_end_min) {
							$app->enqueueMessage(JText::_('Can\'t book in booked timeslot .. Date: '.$pdate), 'error');
							return false;
						}
					}
					}

					if($booking_end_min <= $booking_start_min) {
						$app->enqueueMessage(JText::_('End booking time cannot be less or equal to start booking time (for periodic bookings)'), 'error');
						return false;
					}
				}
			}
		}
		else {
			$db->setQuery("DELETE FROM `#__kirk_bookings` WHERE `booking_master_id` = '".$booking_id."'");
			$db->Query();
		}

		// save booking
		if($booking_id > 0) {
			$db->setQuery("UPDATE `#__kirk_bookings` SET `room_id` = '".$room_id."' , `booking_date` = '".$booking_date."' , `checkin_time` = '".$checkin_time."' , `checkout_time` = '".$checkout_time."' , `customer_name` = '".$data['customer_name']."' , `customer_phone` = '".$data['customer_phone']."' , `customer_email` = '".$data['customer_email']."' , `customer_address` = '".$data['customer_address']."' , `booking_reason` = '".$data['booking_reason']."' , `admin_note` = '".$data['admin_note']."' , `holidays` = '".$holidays_json."' WHERE `id` = '".$booking_id."'");
			$db->Query();

			$db->setQuery("SELECT `holidays` FROM `#__kirk_bookings` WHERE `id` = '".$booking_id."'");
			$holidays = json_decode($db->loadObject()->holidays);
		}
		else {
			$db->setQuery("INSERT INTO `#__kirk_bookings` VALUES ('' , '".$room_id."' , '".$booking_date."' , '".$checkin_time."' , '".$checkout_time."' , '".$data['customer_name']."' , '".$data['customer_phone']."' , '".$data['customer_email']."' , '".$data['customer_address']."', '".$data['booking_reason']."' , '".$data['admin_note']."', '' , 0 , 0, '".$holidays_json."', '', '')");
			$db->Query();
			$data['booking_id'] = $db->insertid();
		}



		if($booking_id > 0) {
			// delete unwanted sub bookings
			$db->setQuery('DELETE FROM `#__kirk_bookings` WHERE `booking_date` NOT IN ("' . implode('" , "' , $period_dates) . '") AND `booking_master_id` = '.$booking_id);
			$db->Query();
		}

		foreach($period_dates as $pdate) {
			if($pdate != $booking_date) {
				$extraquery = '';
				if($booking_id > 0) {
					$extraquery = " AND `booking_master_id` = ".$booking_id ;
					$booking_master_id = $booking_id ;

					if(count($holidays) > 0)
					{
						foreach($holidays as $h)
						{
							$holidaysec_dates = explode('***' , $h);
							$startsec_date = $holidaysec_dates[0];
							$endsec_date = $holidaysec_dates[1];
							$intervalsec = new DateInterval('P1D');
							$beginsec = new DateTime( $startsec_date );
							$endsec = new DateTime( $endsec_date );
							$holidayrangessec = new DatePeriod($beginsec, $intervalsec ,$endsec);

							foreach($holidayrangessec as $hdate)
							{
								$tdate = $hdate->format("Y-m-d") ;
								if($tdate != $booking_date)
								{
									$db->setQuery("DELETE FROM `#__kirk_bookings` WHERE `booking_date` = '".$tdate."' AND `room_id` = '".$room_id."' ".$extraquery);
									$db->Query();
								}
							}
						}
					}
				}
				else {
					$booking_master_id = $data['booking_id'] ;
				}

				$db->setQuery("SELECT * FROM `#__kirk_bookings` WHERE `booking_date` = '".$pdate."' AND `room_id` = '".$room_id."' ".$extraquery);
				$result = $db->loadObjectList();

				if(count($result) > 0) {
					$db->setQuery("UPDATE `#__kirk_bookings` SET `room_id` = '".$room_id."' , `booking_date` = '".$pdate."' , `checkin_time` = '".$checkin_time."' , `checkout_time` = '".$checkout_time."' , `customer_name` = '".$data['customer_name']."' , `customer_phone` = '".$data['customer_phone']."' , `customer_email` = '".$data['customer_email']."' , `customer_address` = '".$data['customer_address']."', `booking_reason` = '".$data['booking_reason']."' , `admin_note` = '".$data['admin_note']."' WHERE `id` = '".$result->id."'");
					$db->Query();
				}
				else {
					$db->setQuery("INSERT INTO `#__kirk_bookings` (`id`, `room_id`, `booking_date`, `checkin_time`, `checkout_time`, `customer_name`, `customer_phone`, `customer_email`, `customer_address`, `booking_reason`, `admin_note`, `booking_master_id`, `holidays`) VALUES ('' , '".$room_id."' , '".$pdate."' , '".$checkin_time."' , '".$checkout_time."' , '".$data['customer_name']."' , '".$data['customer_phone']."' , '".$data['customer_email']."' , '".$data['customer_address']."', '".$data['booking_reason']."' , '".$data['admin_note']."' , '".$booking_master_id."' , '')");
					$db->Query();
				}
			}



		}

		// upload and check images
			$dest_folder = JPATH_COMPONENT . DIRECTORY_SEPARATOR . "event_images" ;
			if(!is_dir($dest_folder)) {
				mkdir($dest_folder , 0777);
			}
			$allowed_extensions = array('jpg' , 'png' , 'jpeg' , 'gif');
			if($event_id > 0) {
				if($data['images'] != '') {
					$images = json_decode($data['images']);
				}
				else {
					$images = array();
				}
			}
			else {
				$images = array();
			}

			jimport('joomla.filesystem.file');
			$files = JFactory::getApplication()->input->files->get('image_upload');
			$delete_images = $_REQUEST['delete_images'];
			for($i=0; $i<5; $i++) {
				if($delete_images[$i] == 1) {
					JFile::delete($dest_folder.DIRECTORY_SEPARATOR.$images[$i]);
					unset($images[$i]);
					$images = array_values($images);
				}
				$filename = '';
				$filename = JFile::makeSafe($files[$i]['name']);
				if($filename != '') {
					$src = $files[$i]['tmp_name'];
					$dest = $dest_folder . DIRECTORY_SEPARATOR . $filename;
					if (in_array(strtolower(JFile::getExt($filename)) , $allowed_extensions))
					{
					   if (JFile::upload($src, $dest))
					   {
						  //$var = 'img'.intval($i+1);echo $var;
						  $images[] = $filename;
					   }
					   else
					   {

					   }
					}
					else
					{

					}
				}
			}
			$str_images = json_encode($images);
			$data['images'] = $str_images;


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
