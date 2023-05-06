<?php
defined('_JEXEC') or die('=;)');
jimport('joomla.application.component.controller');


class RoombookingController extends JControllerLegacy
{
    
    function __construct($config = array())
    {
        parent::__construct($config);
    }
    
   function paypalnotify()
	{
		$app 				= 	JFactory::getApplication();
		$db					=	JFactory::getDBO();
		$input 				= 	$app->input;
		
		// we will code that later
	}
	
	function paypalcancel()
	{
		$app 				= 	JFactory::getApplication();
		$db					=	JFactory::getDBO();
		$input 				= 	$app->input;
	}
	
	function paypalreturn()
	{
		$app 				= 	JFactory::getApplication();
		$db					=	JFactory::getDBO();
		$input 				= 	$app->input;
		$user				=	JFactory::getUser();
		$txn_id				=	$input->getString('tx');
		$st					=	$input->getString('st');
		$item_number		=	$input->getString('item_number');
		$cm					=	$input->getString('cm');
		$amt				=	$input->getString('amt');
		$cm					=	explode('=', $cm);
		$enquiry_id_arr		=	explode(',', $cm[1]);
		$package_id			=	substr($item_number , 6 , strlen($item_number));
		
		$booking_ids 		= 	array();
		
		if(strtolower($st) == 'completed') {
			
			foreach ($enquiry_id_arr as $enquiry_id) {
		
				$db->setQuery("SELECT * FROM `#__kirk_booking_enquiries` WHERE `id` = '".$enquiry_id."'");
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
				
				if(count($result) > 0) {
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
		
		
		
				$db->setQuery("INSERT INTO `#__kirk_bookings` VALUES ('' , '".$enquiry_details->room_id."' , '".$enquiry_details->booking_date."' , '".$checkin_time."' , '".$checkout_time."' , '".$customer_name."' , '".$customer_phone."' , '".$customer_email."' , '".$customer_address."' , '".$booking_reason."' , '', 'paypal', '".$amt."' , 0 , '')");
				$db->Query();
				$new_booking_id = $db->insertid();
				
				$booking_ids[] = $new_booking_id;

				// booking transaction
				$querybooking = $db->getQuery(true);
				$columns = array('booking_id', 'payment_method', 'transaction_id', 'amt', 'transaction_date');
				$values = array($db->Quote($new_booking_id), $db->Quote('paypal'),  $db->Quote($txn_id),  $db->Quote($amt), $db->Quote(date('Y-m-d')));
				$querybooking
					->insert($db->quoteName('#__kirk_transactions'))
					->columns($db->quoteName($columns))
					->values(implode(',', $values));
				$db->setQuery($querybooking);
				$db->execute();
				$newtrnsactionid = $db->insertid();

				$db->setQuery("INSERT INTO  `#__kirk_booking_events` VALUES ('' , '".$new_booking_id."' , 'Event Booked for Booking Id #".$new_booking_id."' , 'Event description for Booking Id #".$new_booking_id."' , 0 , 0 , '".date('Y-m-d' , strtotime('+ 1 year'))."' , 7 , '".$event_required."' , '')");
				$db->Query();

				$db->setQuery("DELETE FROM `#__kirk_booking_enquiries` WHERE `id` = '".$enquiry_id."'");
				$db->Query();

				// booking addons
				$db->setQuery("SELECT * FROM `#__kirk_enquiry_addons` WHERE `enquiry_id` = '".$enquiry_id."'");
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
					$db->setQuery($queryadd);
					$db->execute();
				}

				$db->setQuery("DELETE FROM `#__kirk_enquiry_addons` WHERE `enquiry_id` = '".$enquiry_id."'");
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

			}
			
		}
		
		
		$this->setRedirect(JRoute::_('index.php?option=com_roombooking&view=roombooking&layout=complete&new_booking_id='.implode(',', $booking_ids), false), JText::_('Your payment is succesful'));
	}
	
	function paypalreturncopied()
	{
		$app 				= 	JFactory::getApplication();
		$db					=	JFactory::getDBO();
		$input 				= 	$app->input;
		$user				=	JFactory::getUser();
		$txn_id				=	$input->getString('tx');
		$st					=	$input->getString('st');
		$item_number		=	$input->getString('item_number');
		$package_id			=	substr($item_number , 6 , strlen($item_number));
		
		$db->setQuery("SELECT * FROM `#__jrxfs_packages` WHERE `id` = '".$package_id."'");
		$package_details = $db->loadObject();
		
		if(strtolower($st) == 'completed') {
			$query = $db->getQuery(true);
			$columns = array('user_id', 'package_id', 'storage_added', 'amt_paid', 'buy_date', 'payment_gateway', 'transaction_id', 'status');
			$values = array($user->id, $package_id,  $db->Quote($package_details->storage * 1024),  $db->Quote($package_details->price), $db->Quote(date('Y-m-d')), $db->Quote('paypal'), $db->Quote($txn_id), $db->Quote($st));
			$query
				->insert($db->quoteName('#__jrxfs_user_packages'))
				->columns($db->quoteName($columns))
				->values(implode(',', $values));
			$db->setQuery($query);
			$db->execute();
			$newpackageid = $db->insertid();
			// update user stats
			$query = $db->getQuery(true);
			$query->select(array('*'));
			$query->from($db->quoteName('#__jrxfs_user_stats'));
			$query->where($db->quoteName('user_id') . ' = ' . $user->id);
			$db->setQuery($query);
			$loadstat = $db->loadObject();
			if(count($loadstat) > 0) {
				$query = $db->getQuery(true);
				$query->update($db->quoteName('#__jrxfs_user_stats'))->set(array(
					$db->quoteName('total_storage') . ' = ' . $db->quoteName('total_storage') . ' + '.$package_details->storage))->where(array($db->quoteName('user_id') . ' = '.$user->id
				));
				$db->setQuery($query);
				$db->execute();
			}
			else {
				$query = $db->getQuery(true);
				$query
					->insert($db->quoteName('#__jrxfs_user_stats'))
					->columns($db->quoteName(array('user_id', 'total_storage', 'storage_used')))
					->values(implode(',', array($user->id, $db->Quote($package_details->storage), '0')));
				$db->setQuery($query);
				$db->execute();
			}
		}
		
		
		$this->setRedirect('index.php?option=com_whatreatmentbooking&view=buynow&layout=complete&user_pack_id='.$newpackageid, JText::_('JRXFS_PAYMENT_SUCCESS'));
	}
   
	function stripeprocessing()
	{
		$app 				= 	JFactory::getApplication();
		$params 			= 	$app->getParams();
		$session 			= 	JFactory::getSession();
		$db					=	JFactory::getDBO();
		$input 				= 	$app->input;
		$user				=	JFactory::getUser();
		$token				=	$input->get('token');
		$amount				=	$input->get('amount');
		$item_name			=	$input->getString('item_name');
		$item_number		=	$input->getString('item_number');
		$email				=	$input->getString('email');
		$name				=	$input->getString('name');
		$enquiry_id_arr		=	explode(',', $input->getString('enquiry_id'));
		$booking_id			=	array();
		
		if (!empty($token)) {
			require_once JPATH_COMPONENT_SITE.DIRECTORY_SEPARATOR.'library'.DIRECTORY_SEPARATOR.'StripePayment.php';
			$stripePayment = new StripePayment();
			
			$stripeResponse = $stripePayment->chargeAmountFromCard($_POST);
			$amount = $stripeResponse["amount"] /100;
			
			$param_type = 'ssdssss';
			$param_value_array = array( $email, $item_number, $amount, $stripeResponse["currency"], $stripeResponse["balance_transaction"], $stripeResponse["status"], json_encode($stripeResponse) );
			//echo '<pre>';print_r($stripeResponse);exit;
			if ($stripeResponse['amount_refunded'] == 0 && empty($stripeResponse['failure_code']) && $stripeResponse['paid'] == 1 && $stripeResponse['captured'] == 1 && $stripeResponse['status'] == 'succeeded') {
				
				foreach ($enquiry_id_arr as $enquiry_id) {
					$db->setQuery("SELECT * FROM `#__kirk_booking_enquiries` WHERE `id` = '".$enquiry_id."'");
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
					
					if(count($result) > 0) {
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



					$db->setQuery("INSERT INTO `#__kirk_bookings` VALUES ('' , '".$enquiry_details->room_id."' , '".$enquiry_details->booking_date."' , '".$checkin_time."' , '".$checkout_time."' , '".$customer_name."' , '".$customer_phone."' , '".$customer_email."' , '".$customer_address."' , '".$booking_reason."' , '', 'stripe', '".$amount."' , 0 , '')");
					$db->Query();
					$new_booking_id = $db->insertid();
					
					$booking_id[] = $new_booking_id;

					// booking transaction
					$querybooking = $db->getQuery(true);
					$columns = array('booking_id', 'payment_method', 'transaction_id', 'amt', 'transaction_date');
					$values = array($db->Quote($new_booking_id), $db->Quote('stripe'),  $db->Quote($stripeResponse["id"]),  $db->Quote($amount), $db->Quote(date('Y-m-d')));
					$querybooking
						->insert($db->quoteName('#__kirk_transactions'))
						->columns($db->quoteName($columns))
						->values(implode(',', $values));
					$db->setQuery($querybooking);
					$db->execute();
					$newtrnsactionid = $db->insertid();

					$db->setQuery("INSERT INTO  `#__kirk_booking_events` VALUES ('' , '".$new_booking_id."' , 'Event Booked for Booking Id #".$new_booking_id."' , 'Event description for Booking Id #".$new_booking_id."' , 0 , 0 , '".date('Y-m-d' , strtotime('+ 1 year'))."' , 7 , '".$event_required."' , '')");
					$db->Query();

					$db->setQuery("DELETE FROM `#__kirk_booking_enquiries` WHERE `id` = '".$enquiry_id."'");
					$db->Query();

					// booking addons
					$db->setQuery("SELECT * FROM `#__kirk_enquiry_addons` WHERE `enquiry_id` = '".$enquiry_id."'");
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
						$db->setQuery($queryadd);
						$db->execute();
					}
					
					$db->setQuery("DELETE FROM `#__kirk_enquiry_addons` WHERE `enquiry_id` = '".$enquiry_id."'");
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

				}
				
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
			}
		}
		
		$this->setRedirect(JRoute::_('index.php?option=com_roombooking&view=roombooking&layout=complete&new_booking_id='.implode(',', $booking_id), false), JText::_('Your payment is succesful'));
		
	}

	function pay_later_process()
	{
		$app = JFactory::getApplication();
		$params = $app->getParams();
		$session = JFactory::getSession();
		$db = JFactory::getDBO();
		$input = $app->input;
		$user = JFactory::getUser();
		$enquiry_id_arr = explode(',', $input->getString('enquiry_id'));
		$amount = $input->getString('amount');
		$booking_id = array();

		foreach ($enquiry_id_arr as $enquiry_id) {
			$db->setQuery("SELECT * FROM `#__kirk_booking_enquiries` WHERE `id` = '" . $enquiry_id . "'");
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

			$db->setQuery("SELECT * FROM `#__kirk_bookings` WHERE `booking_date` = '" . $enquiry_details->booking_date . "' AND `room_id` = '" . $enquiry_details->room_id . "'");
			$result = $db->loadObjectList();

			$booking_start = explode('.', $checkin_time);
			$booking_start_min = $booking_start[0] * 60 + $booking_start[1];
			$booking_out = explode('.', $checkout_time);
			$booking_end_min = $booking_out[0] * 60 + $booking_out[1];

			if (count($result) > 0) {
				foreach ($result as $r) {
					$existing_booking_start = explode('.', $r->checkin_time);
					$existing_booking_start_min = $existing_booking_start[0] * 60 + $existing_booking_start[1];
					$existing_booking_out = explode('.', $r->checkout_time);
					$existing_booking_end_min = $existing_booking_out[0] * 60 + $existing_booking_out[1];


					if ($booking_start_min >= $existing_booking_start_min && $booking_start_min < $existing_booking_end_min) {
						$app->enqueueMessage(JText::_('Can\'t enquire in booked timeslot'), 'error');
						//$app->redirect(JRoute::_('index.php?option=com_roombooking&view=booking_enquiry&layout=edit&id='.$id));
					}

					if ($booking_end_min >= $existing_booking_start_min && $booking_end_min <= $existing_booking_end_min) {
						$app->enqueueMessage(JText::_('Can\'t enquire in booked timeslot'), 'error');
						//$app->redirect(JRoute::_('index.php?option=com_roombooking&view=booking_enquiry&layout=edit&id='.$id));
					}
				}
			}

		}

		$this->setRedirect(JRoute::_('index.php?option=com_roombooking&view=roombooking&layout=complete_enquiry&enquiry_id=' . $input->getString('enquiry_id'), false), JText::_('Your booking enquiry is successful'));
	}
    
} // class
