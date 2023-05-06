<?php
defined('_JEXEC') or die();
jimport('joomla.application.component.controllerform');

class RoombookingControllerRoombooking extends JControllerForm
{

	protected	$option 		= 'com_roombooking';
	
    
    function ajaxbookingfetch()
	{
		$app = JFactory::getApplication();
		$input = $app->input;
		$db = JFactory::getDBO();
		$db->setQuery("SELECT * FROM `#__kirk_rooms` WHERE `status` = 1");
		$rooms = $db->loadObjectList();
		$roombookingdetails = array();
		$booking_date = $input->request->get('booking_date');
		$span_date = $booking_date ; 
		$addtimerange = $input->request->get('addtimerange');
		if($addtimerange == 1) {
			$date_range = $input->request->get('date_range');
			$span_date = date('Y-m-d' , strtotime($booking_date.' + '. $date_range.' days' ));
		} 
		
		
		$date_range_array = $this->getDatesFromRange($booking_date , $span_date , 'Y-m-d');  //print_r($date_range_array);
		?>
		<h3><?php echo JText::_( 'ROOMBOOKING') ; ?></h3>
		<div class="col-md-12">
			<div class="redblock">&nbsp;</div><div style="float: left; padding: 0px 5px 0 5px;">Booked</div><div class="greenblock">&nbsp;</div>&nbsp;<div style="float: left; padding: 0px 5px 0 5px;">Available</div><div class="yellowblock">&nbsp;</div>&nbsp;<div style="float: left; padding: 0px 5px 0 5px;">Provisionally Booked</div>
		</div>
		<table cellpadding="0" cellspacing="0" class="table-striped timetable">
		<tr>
		<th width="250"><?php echo JText::_('DATE'); ?></th>
		<?php
			for($r=0; $r<24; $r++) {
			?>
			<th colspan="4" class="rightruler">
			<?php
				echo date("H", strtotime("00-00-00 $r:00:00"));
			?>
			</th>
			<?php	
			}
		?>
		</tr>
		<?php
		foreach($date_range_array as $dr) {
		$date_c = date('Y-m-d' , strtotime($dr));
		?>
		<tr>
			<th colspan="97" align="center" style="text-align: center;"><?php echo JText::sprintf( 'ROOMBOOKINGHEADING' , date('l' , strtotime($dr)) , date('d/m/Y' , strtotime($dr))) ; ?></th>
		</tr>
		<?php
		foreach($rooms as $room) {
		$db->setQuery("SELECT * FROM `#__kirk_bookings` WHERE `booking_date` = '".$date_c."' AND `room_id` = '".$room->id."'");
		$bookingdetails = $db->loadObjectList();
		$roomtimearray = array();
		for($k = 0 ; $k < 96 ; $k++) {
			$roomtimearray[$k] = 0;
		}
		$timeslotarray = array();
		foreach($bookingdetails as $bd) {
			$checkin_time = $bd->checkin_time;
			$c1 = explode('.' , $checkin_time);
			$c1index = $c1[0] * 4 + ($c1[1] / 15);
			$checkout_time = $bd->checkout_time;
			$c2 = explode('.' , $checkout_time);
			$c2index = $c2[0] * 4 + ($c2[1] / 15);
			for($j=$c1index ; $j<$c2index ; $j++) {
				$timeslotarray[] = $j;
			}
			$timeslotarray = array_unique($timeslotarray);

			foreach($timeslotarray as $ta) {
				$roomtimearray[$ta] = 1;
			}
		}
			
		$db->setQuery("SELECT * FROM `#__kirk_booking_enquiries` WHERE `booking_date` = '".$date_c."' AND `room_id` = '".$room->id."'");
		$enquirydetails = $db->loadObjectList();
		$enquirytimearray = array();
		for($k = 0 ; $k < 96 ; $k++) {
			$enquirytimearray[$k] = 0;
		}
		$enquiredtimeslotarray = array();
		foreach($enquirydetails as $bd) {
			$checkin_time = $bd->checkin_time;
			$c1 = explode('.' , $checkin_time);
			$c1index = $c1[0] * 4 + ($c1[1] / 15);
			$checkout_time = $bd->checkout_time;
			$c2 = explode('.' , $checkout_time);
			$c2index = $c2[0] * 4 + ($c2[1] / 15);
			for($j=$c1index ; $j<$c2index ; $j++) {
				$enquiredtimeslotarray[] = $j;
			}
			$enquiredtimeslotarray = array_unique($enquiredtimeslotarray);

			foreach($enquiredtimeslotarray as $ta) {
				$enquiredtimeslotarray[$ta] = 1;
			}
		}
		?>
		
		<tr class="timebar">
		<td width="250" class="roomname"><h5><?php echo $room->room_name ; ?></h5></td>
		<?php
		for($i = 0; $i <= 95 ; $i ++) {
		$slotclass = '';
		if($roomtimearray[$i] == 1) {
			$slotclass = 'booked';
		}
		elseif($enquiredtimeslotarray[$i] == 1) {
			$slotclass = 'enquired';
		}	
		else {
			$slotclass = 'notbooked';
		}
		?>
		<td class="<?php echo $slotclass ; ?>"></td>
		<?php	
		}
		?>
		</tr>
		
		
		<?php	
		}
		?>
		<tr><td width="97" height="10"></td></tr>
		<tr>
		<th width="250"></th>
		<?php
			for($r=0; $r<24; $r++) {
			?>
			<th colspan="4" class="rightruler">
			<?php
				echo date("H", strtotime("00-00-00 $r:00:00"));
			?>
			</th>
			<?php	
			}
		?>
		</tr>
		<?php	
		}
		?>
		</table>
		<?php
		$app->close();
	}
	
	function getDatesFromRange($start, $end, $format) { 
		$array = array(); 
		$interval = new DateInterval('P1D'); 

		$realEnd = new DateTime($end); 
		$realEnd->add($interval); 

		$period = new DatePeriod(new DateTime($start), $interval, $realEnd); 

		foreach($period as $date) {                  
			$array[] = $date->format($format);  
		} 
		return $array; 
	} 
	
	function enquiry() {
		$app = JFactory::getApplication();
		$params = JComponentHelper::getParams('com_roombooking');
		$db = JFactory::getDBO();
		$input = $app->input;
		$msg = array();
		$room_id = $input->get('room_id');
		$customer_address = $input->getString('customer_address');
		$customer_name = $input->getString('customer_name');
		$customer_phone = $input->getString('customer_phone');
		$customer_email = $input->getString('customer_email');
		$customer_address = $input->getString('customer_address');
		$checkin_time_hour = $input->get('checkin_hour');
		$checkin_time_min = $input->get('checkin_min');
		$checkout_time_hour = $input->get('checkout_hour');
		$checkout_time_min = $input->get('checkout_min');
		$event_required = $input->get('event_required');
		$booking_reason = $input->getString('booking_reason');
		$payment_gateway = $input->getString('payment_gateway');
        // for now we are disabling payment gateway
        $payment_gateway = 'pay_later';
		$itemid = $input->get('Itemid');
		$booking_date = $input->getString('booking_enquiry_date');
		$add_info = $input->getString('add_info');
		$business_name = $input->getString('business_name');
		$date = str_replace('/', '-', $booking_date );
		$newDate = date("Y-m-d", strtotime($date));
    	
		$rooms = array();
			
		if ($room_id > 0) {
			$rooms[] = $room_id;
		}
		else {
			$db->setQuery("SELECT * FROM `#__kirk_rooms` WHERE `status` = 1");
				
			foreach ($db->loadObjectList() as $room)
			{
				$rooms[] = $room->id;
			}
		}
		
		$enquiry = array();
		
		$no_of_rooms = count($rooms);
		
		foreach ($rooms as $room_id) {
			$checkin_time = $checkin_time_hour.'.'.$checkin_time_min;
			$checkout_time = $checkout_time_hour.'.'.$checkout_time_min;
			$amount = intval($input->getString('total_price')) / $no_of_rooms;
		
			$query = $db->getQuery(true);
			$query->select(array('*'));
			$query->from($db->quoteName('#__kirk_bookings'));
			$query->where($db->quoteName('booking_date') . ' = ' . $db->quote($newDate));
			
			$query->where($db->quoteName('room_id') . ' = ' . $db->quote($room_id));
			
		
			$db->setQuery($query);
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
						$app->enqueueMessage(JText::_('It looks like you are trying to book an unavailable slot'), 'error');
						$app->redirect(JRoute::_('index.php?Itemid='.$itemid));
					}

					if($booking_end_min >= $existing_booking_start_min && $booking_end_min <= $existing_booking_end_min) {
						$app->enqueueMessage(JText::_('It looks like you are trying to book an unavailable slot'), 'error');
						$app->redirect(JRoute::_('index.php?Itemid='.$itemid));
					}
				}
			}
		
			$db->setQuery("SELECT * FROM `#__kirk_booking_enquiries` WHERE `booking_date` = '".$newDate."' AND `room_id` = '".$room_id."'");

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
						$app->enqueueMessage(JText::_('It looks like you are trying to book an unavailable slot'), 'error');
						$app->redirect(JRoute::_('index.php?Itemid='.$itemid));
					}

					if($booking_end_min >= $existing_booking_start_min && $booking_end_min <= $existing_booking_end_min) {
						$app->enqueueMessage(JText::_('It looks like you are trying to book an unavailable slot'), 'error');
						$app->redirect(JRoute::_('index.php?Itemid='.$itemid));
					}
				}
			}
		
			$db->setQuery("INSERT INTO `#__kirk_booking_enquiries` VALUES ('' , '".$room_id."' , '".$newDate."' , '".$checkin_time."' , '".$checkout_time."' , '".$customer_name."' , '".$customer_phone."' , '".$customer_email."' , '".$customer_address."', '".$booking_reason."' , '' , '".$event_required."' , '".$payment_gateway."' , '".$amount."', '".$business_name."', '".$add_info."')");
			$db->Query();

			$new_enquiry_id = $db->insertid();
			$enquiry[]		= $new_enquiry_id;
			
			// getting all addons
			$query = $db->getQuery(true);
			$query->select(array('*'));
			$query->from($db->quoteName('#__kirk_addons'));
			$query->where($db->quoteName('status') . ' = 1');

			$db->setQuery($query);
			$addons = $db->loadObjectlist();

			foreach ($addons as $an)
			{
				$adonvalue = $input->get('addon_'.$an->id);

				if ($adonvalue == 1)
				{
					$query = $db->getQuery(true);
					$columns = array('enquiry_id', 'addon_id', 'price');
					$values = array($new_enquiry_id, $an->id, $db->quote($an->price));

					// Prepare the insert query.
					$query
						->insert($db->quoteName('#__kirk_enquiry_addons'))
						->columns($db->quoteName($columns))
						->values(implode(',', $values));

					// Set the query using our newly populated query object and execute it.
					$db->setQuery($query);
					$db->execute();
				}
			}
		
			if($params->get('notifyemail') != '') {
				$mailer = JFactory::getMailer();
				$config = JFactory::getConfig();
				$sender = array( 
					$config->get( 'mailfrom' ),
					$config->get( 'fromname' ) 
				);

				$db->setQuery("SELECT * FROM `#__user_usergroup_map` WHERE `group_id` IN (7 , 8)");
				$users = $db->loadObjectList();

				$mailer->setSender($sender);

				$recipient = $params->get('notifyemail') ;	
				$mailer->addRecipient($recipient);

				$body   = '<h2>Someone Enquired about a slot</h2>'
					. '<table border="0" cellpadding="0" cellspacing="0">'
					. '<tr><td><b>Customer Name</b></td><td>'.$customer_name.'</td></tr>'
					. '<tr><td><b>Customer Email</b></td><td>'.$customer_email.'</td></tr>'
					. '<tr><td><b>Customer Phone</b></td><td>'.$customer_phone.'</td></tr>'
					. '<tr><td><b>Customer Adress</b></td><td>'.$customer_address.'</td></tr>'
					. '<tr><td><b>Booking Date</b></td><td>'.$booking_date.'</td></tr>'
					. '<tr><td><b>Checking Time</b></td><td>'.$checkin_time.'</td></tr>'
					. '<tr><td><b>Checkout Time</b></td><td>'.$checkout_time.'</td></tr>'
					. '<tr><td><b>Booking Reason</b></td><td>'.$booking_reason.'</td></tr>'
					. '<tr><td><b>Price</b></td><td>£'.$amount.'</td></tr>'
					. '<tr><td colspan="2" height="20"></td></tr>'
					. '<tr><td colspan="2">Thanks</td></tr>'
					. '<tr><td colspan="2"></td></tr>'
					. '</table>';
				$mailer->isHtml(true);
				$mailer->Encoding = 'base64';
				$mailer->setBody($body);
				$send = $mailer->Send();
				if ( $send !== true ) {
					echo 'Error sending email: ';
				} else {
					echo 'Mail sent';
				}

				$mailer_second = JFactory::getMailer();	
				$mailer_second->setSender($sender);
				$recipient = $customer_email ;	
				$mailer_second->setSubject('Thank you for your Enquiry your room is now provisionally booked');
				$mailer_second->addRecipient($recipient);	
				$body   = '<h2>Thank you for your Enquiry your room is now provisionally booked</h2>'
					.'<table border="0" width="100%">'
					. '<tr><td>Thank you for your Enquiry your room is now provisionally booked</td></tr>'
					. '<tr><td><b>Booking Date</b>&nbsp;'.$booking_date.'</td></tr>'
					. '<tr><td><b>Checking Time</b>&nbsp;'.$checkin_time.'</td></tr>'
					. '<tr><td><b>Checkout Time</b>&nbsp;'.$checkout_time.'</td></tr>'
					. '<tr><td>Please read the attached conditions of hire:-</td></tr>'
					. '<tr><td><a href="emailto:bookings@kirksantonvillagehall.co.uk"><b>bookings@kirksantonvillagehall.co.uk</b></a></td></tr>'
					. '<tr><td height="20"></td></tr>'
					. '<tr><td>Kind Regards</td></tr>'
					. '<tr><td>Kirksanton Village Hall</td></tr>'
					. '</table>';
				$mailer_second->isHtml(true);
				$mailer_second->Encoding = 'base64';
				$mailer_second->setBody($body);
				//$mailer_second->addAttachment(JPATH_COMPONENT.'/assets/Hiring-Agreement-2019.pdf');
				$mailer_second->addAttachment(JPATH_COMPONENT.'/assets/Kirksanton-Village-Hall-Standard-Conditions-of-Hire.pdf');
				$send = $mailer_second->Send();
				if ( $send !== true ) {
					echo 'Error sending email: ';
				} else {
					echo 'Mail sent';
				}

			}
		
		}
		
		//$app->redirect(JRoute::_('index.php?Itemid='.$itemid) , 'Thank you for your enquiry we will confirm your booking soon');
		//$app->redirect(JRoute::_('index.php?option=com_roombooking&view=roombooking&layout=payment&enquiry_id='.implode(',', $enquiry).'&payment_gateway='.$payment_gateway.'&Itemid='.$itemid, false), 'Complete your payment now');
		//$app->redirect(JRoute::_('index.php?option=com_roombooking&view=roombooking&layout=payment&enquiry_id='.implode(',', $enquiry).'&payment_gateway='.$payment_gateway.'&Itemid='.$itemid, false), 'Thank you for your enquiry we will confirm your booking soon');
		//$app->redirect(JRoute::_('index.php?option=com_roombooking&view=roombooking&layout=complete&enquiry_id='.implode(',', $enquiry).'&Itemid='.$itemid, false), 'Thank you for your enquiry we will confirm your booking soon');
		$this->setRedirect(JRoute::_('index.php?option=com_roombooking&view=roombooking&layout=complete_enquiry&enquiry_id=' . $new_enquiry_id, false), JText::_('Thank you for your enquiry we will confirm your booking soon'));
		
	}
	
}