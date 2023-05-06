<?php
defined('_JEXEC') or die();
jimport('joomla.application.component.controllerform');

class RoombookingControllerBooking_enquiry extends JControllerForm
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

		if ($user->authorise('core.edit', 'com_roombooking.booking_enquiry.'.$recordId)) {
			return true;
		}


		return parent::allowEdit($data, $key);
	}

	function delete($key = NULL, $urlVar = NULL) {
		$db = JFactory::getDBO();
		$cid = JRequest::getVar('cid');
		foreach($cid as $id) {
			$db->setQuery("DELETE FROM `#__kirk_booking_enquiries` WHERE `id` = '".$id."'");
			$db->Query();
		}

		$this->setRedirect('index.php?option=com_roombooking&view=booking_enquiries' , JText::_('DELETED'));
	}

	function book()
	{
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		$input = $app->input;
		$id = $input->get('id');


		$db->setQuery("SELECT * FROM `#__kirk_booking_enquiries` WHERE `id` = '".$id."'");
		$enquiry_details = $db->loadObject();
		//$enquiry_details->room_id
		$rooms = array();

		// code to convert enquiry into booking
		$checkin_time = $enquiry_details->checkin_time;
		$checkout_time = $enquiry_details->checkout_time;

		$checkin = explode('.', $enquiry_details->checkin_time);
		$checkout = explode('.', $enquiry_details->checkout_time);

		$checkin_time_hour = $checkin[0];
		$checkin_time_min = $checkin[1];
		$checkout_time_hour = $checkout[0];
		$checkout_time_min = $checkout[1];

		$customer_name = $enquiry_details->customer_name;
		$customer_email = $enquiry_details->customer_email;
		$customer_phone = $enquiry_details->customer_phone;
		$customer_address = $enquiry_details->customer_address;

		$db->setQuery("SELECT * FROM `#__kirk_bookings` WHERE `booking_date` = '".$enquiry_details->booking_date."' AND `room_id` = '".$enquiry_details->room_id."'");
		$result = $db->loadObjectList();

		$booking_start = explode('.' , $checkin_time) ;
		$booking_start_min = $booking_start[0] * 60 + $booking_start[1];
		$booking_out = explode('.' , $checkout_time) ;
		$booking_end_min = $booking_out[0] * 60 + $booking_out[1];

		$amount = $result->amount;

		if (count($result) > 0)
		{
			foreach($result as $r) {
				$existing_booking_start = explode('.' , $r->checkin_time) ;
				$existing_booking_start_min = $existing_booking_start[0] * 60 + $existing_booking_start[1];
				$existing_booking_out = explode('.' , $r->checkout_time) ;
				$existing_booking_end_min = $existing_booking_out[0] * 60 + $existing_booking_out[1];



				if($booking_start_min >= $existing_booking_start_min && $booking_start_min < $existing_booking_end_min) {
					$app->enqueueMessage(JText::_('Can\'t enquire in booked timeslot'), 'error');
					//$app->redirect(JRoute::_('index.php?option=com_roombooking&view=booking_enquiry&layout=edit&id='.$id));
				}

				if($booking_end_min >= $existing_booking_start_min && $booking_end_min <= $existing_booking_end_min) {
					$app->enqueueMessage(JText::_('Can\'t enquire in booked timeslot'), 'error');
					//$app->redirect(JRoute::_('index.php?option=com_roombooking&view=booking_enquiry&layout=edit&id='.$id));
				}
			}
		}



		$db->setQuery("INSERT INTO `#__kirk_bookings` VALUES ('' , '".$enquiry_details->room_id."' , '".$enquiry_details->booking_date."' , '".$checkin_time."' , '".$checkout_time."' , '".$customer_name."' , '".$customer_phone."' , '".$customer_email."' , '".$customer_address."' , '".$enquiry_details->booking_reason."' , '', '".$enquiry_details->payment_gateway."', '".$amount."' , 0 , '', '".$enquiry_details->business_name."', '".$enquiry_details->add_info."')");
		$db->Query();
		$new_booking_id = $db->insertid();

		$booking_id[] = $new_booking_id;



		$db->setQuery("INSERT INTO  `#__kirk_booking_events` VALUES ('' , '".$new_booking_id."' , 'Event Booked for Booking Id #".$new_booking_id."' , 'Event description for Booking Id #".$new_booking_id."' , 0 , 0 , '".date('Y-m-d' , strtotime('+ 1 year'))."' , 7 , '".$enquiry_details->event_required."' , '')");
		$db->Query();

		$db->setQuery("DELETE FROM `#__kirk_booking_enquiries` WHERE `id` = '".$id."'");
		$db->Query();

		// booking addons
		$db->setQuery("SELECT * FROM `#__kirk_enquiry_addons` WHERE `enquiry_id` = '".$id."'");
		$enquiry_addons = $db->loadObjectList();

		foreach ($enquiry_addons as $enad)
		{
			$queryadd = $db->getQuery(true);
			$columns = array('booking_id', 'addon_id', 'price');
			$values = array($new_booking_id, $enad->id,  $enad->price);
			$queryadd
				->insert($db->quoteName('#__kirk_booking_addons'))
				->columns($db->quoteName($columns))
				->values(implode(',', $values));
			$db->setQuery($queryadd);echo $db->getQuery();
			$db->execute();
		}

		$db->setQuery("DELETE FROM `#__kirk_enquiry_addons` WHERE `enquiry_id` = '".$id."'");
		$db->Query();

		// send email to enquirer
		$mailer = JFactory::getMailer();
		$config = JFactory::getConfig();
		$sender = array(
			$config->get( 'mailfrom' ),
			$config->get( 'fromname' )
		);

		$mailer->setSender($sender);

		$recipient = $customer_email;
		$mailer->addRecipient($recipient);
		$mailer->setSubject('Thankyou for booking a room');
		$body   = '<h2>Thankyou for booking a room</h2>'
			. '<table border="0" width="100%">'
			. '<tr><td>Dear '.$customer_name.'</td></tr>'
			. '<tr><td>Thankyou for booking a room, your booking has now been processed and approved please refer to the documentation supplied for information regarding your booking.</td></tr>'
			. '<tr><td height="20"></td></tr>'
			. '<tr><td>Any queries please contact</td></tr>'
			. '<tr><td height="20"></td></tr>'
			. '<tr><td>MISS VICKY MARKENDALE</td></tr>'
			. '<tr><td height="20"></td></tr>'
			. '<tr><td>Kirksanton Village Hall,<br/>Kirksanton ,<br/>Cumbria LA18 4NN</td></tr>'
			. '<tr><td height="20"></td></tr>'
			. '<tr><td>Telephone - 01229 771680</td></tr>'
			. '<tr><td height="20"></td></tr>'
			. '<tr><td><a href="emailto:bookings@kirksantonvillagehall.co.uk">bookings@kirksantonvillagehall.co.uk</a></td></tr>'

			. '<tr><td height="20"></td></tr>'
			. '<tr><td>Kind Regards</td></tr>'
			. '<tr><td height="20"></td></tr>'
			. '<tr><td>Kirksanton Village Hall</td></tr>'
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
		$this->setRedirect('index.php?option=com_roombooking&view=bookings' , JText::_('Booking Successful'));
	}

 	function book1() {
		$db = JFactory::getDBO();
		$app = JFactory::getApplication();
		$input = $app->input;
		$checkin_time_hour = $input->get('checkin_time_hour');
		$checkin_time_min = $input->get('checkin_time_min');
		$checkout_time_hour = $input->get('checkout_time_hour');
		$checkout_time_min = $input->get('checkout_time_min');
		$formData = new JRegistry($input->get('jform', '', 'array'));
		$id = $formData->get('id');
		$room_id = $formData->get('room_id');
		$customer_name = $formData->get('customer_name');
		$customer_phone = $formData->get('customer_phone');
		$customer_email = $formData->get('customer_email');
		$customer_address = $formData->get('customer_address');
		$booking_date = $formData->get('booking_date');
		$booking_reason = $formData->get('booking_reason');
		$admin_note = $formData->get('admin_note');
		$event_required = $formData->get('event_required');

		$checkin_time = $checkin_time_hour.'.'.$checkin_time_min;
		$checkout_time = $checkout_time_hour.'.'.$checkout_time_min;

		$db->setQuery("SELECT * FROM `#__kirk_bookings` WHERE `booking_date` = '".$booking_date."' AND `room_id` = '".$room_id."'");
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
				$app->enqueueMessage(JText::_('Can\'t enquire in booked timeslot'), 'error');
				$app->redirect(JRoute::_('index.php?option=com_roombooking&view=booking_enquiry&layout=edit&id='.$id));
			}

			if($booking_end_min >= $existing_booking_start_min && $booking_end_min <= $existing_booking_end_min) {
				$app->enqueueMessage(JText::_('Can\'t enquire in booked timeslot'), 'error');
				$app->redirect(JRoute::_('index.php?option=com_roombooking&view=booking_enquiry&layout=edit&id='.$id));
			}
		}
		}



		$db->setQuery("INSERT INTO `#__kirk_bookings` VALUES ('' , '".$room_id."' , '".$booking_date."' , '".$checkin_time."' , '".$checkout_time."' , '".$customer_name."' , '".$customer_phone."' , '".$customer_email."' , '".$customer_address."' , '".$booking_reason."' , '', 'paypal', '' , 0 , '')");
		$db->Query();
		$new_booking_id = $db->insertid();

		$db->setQuery("INSERT INTO  `#__kirk_booking_events` VALUES ('' , '".$new_booking_id."' , 'Event Booked for Booking Id #".$new_booking_id."' , 'Event description for Booking Id #".$new_booking_id."' , 0 , 0 , '".date('Y-m-d' , strtotime('+ 1 year'))."' , 7 , '".$event_required."' , '')");
		$db->Query();

		$db->setQuery("DELETE FROM `#__kirk_booking_enquiries` WHERE `id` = '".$id."'");
		$db->Query();

		// send email to enquirer
		$mailer = JFactory::getMailer();
		$config = JFactory::getConfig();
		$sender = array(
			$config->get( 'mailfrom' ),
			$config->get( 'fromname' )
		);

		$mailer->setSender($sender);

		$recipient = $customer_email;
		$mailer->addRecipient($recipient);
		$mailer->setSubject('Thankyou for booking a room');
		$body   = '<h2>Thankyou for booking a room</h2>'
			. '<table border="0" width="100%">'
			. '<tr><td>Dear '.$customer_name.'</td></tr>'
			. '<tr><td>Thankyou for booking a room, your booking has now been processed and approved please refer to the documentation supplied for information regarding your booking.</td></tr>'
			. '<tr><td height="20"></td></tr>'
			. '<tr><td>Any queries please contact</td></tr>'
			. '<tr><td height="20"></td></tr>'
			. '<tr><td>MISS VICKY MARKENDALE</td></tr>'
			. '<tr><td height="20"></td></tr>'
			. '<tr><td>Kirksanton Village Hall,<br/>Kirksanton ,<br/>Cumbria LA18 4NN</td></tr>'
			. '<tr><td height="20"></td></tr>'
			. '<tr><td>Telephone - 01229 771680</td></tr>'
			. '<tr><td height="20"></td></tr>'
			. '<tr><td><a href="emailto:bookings@kirksantonvillagehall.co.uk">bookings@kirksantonvillagehall.co.uk</a></td></tr>'

			. '<tr><td height="20"></td></tr>'
			. '<tr><td>Kind Regards</td></tr>'
			. '<tr><td height="20"></td></tr>'
			. '<tr><td>Kirksanton Village Hall</td></tr>'
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

		$app->redirect(JRoute::_('index.php?option=com_roombooking&view=bookings') , 'Booking done successfully and also added event for it');
	}
}
