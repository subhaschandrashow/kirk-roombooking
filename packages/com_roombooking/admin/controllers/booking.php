<?php
defined('_JEXEC') or die();
jimport('joomla.application.component.controllerform');

class RoombookingControllerBooking extends JControllerForm
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
 
		if ($user->authorise('core.edit', 'com_roombooking.booking.'.$recordId)) {
			return true;
		}


		return parent::allowEdit($data, $key);
	}
	
	function delete($key = NULL, $urlVar = NULL) {
		$db = JFactory::getDBO();
		$cid = JRequest::getVar('cid');
		foreach($cid as $id) {
			$db->setQuery("DELETE FROM `#__kirk_bookings` WHERE `id` = '".$id."'");
			$db->Query();
		}
		
		$this->setRedirect('index.php?option=com_roombooking&view=bookings' , JText::_('DELETED'));
	}
	
	function refund()
	{
		$app 				= 	JFactory::getApplication();
		$params 			= 	JComponentHelper::getParams('com_roombooking');
		
		
		$refunded = false;
		
		$db = JFactory::getDBO();
		$id = JRequest::getVar('id');
		$db->setQuery("SELECT * FROM `#__kirk_bookings` WHERE `id` = '".$id."'");
		$booking = $db->loadObject();
		
		if ($booking->payment_gateway == 'stripe') {
			$db->setQuery("SELECT * FROM `#__kirk_transactions` WHERE `booking_id` = '".$id."'");
			$transaction = $db->loadObject();
			
			$stripe_secret_key = $params->get('stripe_secret_key');
			
			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/refunds');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "charge=".$transaction->transaction_id);
			curl_setopt($ch, CURLOPT_USERPWD, $stripe_secret_key . ':' . '');

			$headers = array();
			$headers[] = 'Content-Type: application/x-www-form-urlencoded';
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

			$result = curl_exec($ch);
			if (curl_errno($ch)) {
				echo 'Error:' . curl_error($ch);
			}
			curl_close($ch);
			$obj = json_decode($str);
			
			if (strtolower($obj->status) == 'succeeded') {
				$refunded = true;
				// refund transaction
				$query = $db->getQuery(true);
				$columns = array('room_id', 'booking_date', 'checkin_time', 'checkout_time', 'customer_name', 'customer_phone', 'customer_email', 'customer_address', 'booking_reason', 'admin_note', 'payment_gateway', 'amount', 'refund_trx_id');
					$values = array($db->Quote($booking->room_id), $db->Quote($booking->booking_date),  $db->Quote($booking->checkin_time),  $db->Quote($booking->checkout_time), $db->Quote($booking->customer_name), $db->Quote($booking->customer_phone), $db->Quote($booking->customer_email), $db->Quote($booking->customer_address), $db->Quote($booking->booking_reason), $db->Quote($booking->admin_note), $db->Quote($booking->payment_gateway), $db->Quote($booking->amount), $db->Quote($obj->charge));
					$query
						->insert($db->quoteName('#__kirk_refunds'))
						->columns($db->quoteName($columns))
						->values(implode(',', $values));
					$db->setQuery($querybooking);
					$db->execute();
				}
		}
		
		if ($booking->payment_gateway == 'paypal') {
			$paypal_restapi_client_id = $params->get('paypal_restapi_client_id');
			$paypal_restapi_secret = $params->get('paypal_restapi_secret');
			
			$db->setQuery("SELECT * FROM `#__kirk_transactions` WHERE `booking_id` = '".$id."'");
			$transaction = $db->loadObject();
			
			$curl = curl_init();
			curl_setopt_array($curl, array(
			CURLOPT_URL => "https://api.sandbox.paypal.com/v1/oauth2/token",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_USERPWD => $paypal_restapi_client_id.":".$paypal_restapi_secret,
			CURLOPT_POSTFIELDS => "grant_type=client_credentials",
			CURLOPT_HTTPHEADER => array(
			"Accept: application/json",
			"Accept-Language: en_US"
			),
			));

			$result= curl_exec($curl);

			$array=json_decode($result, true); 
			$access_token=$array['access_token'];
			$app_id=$array['app_id'];
			
			$url = "https://api-m.sandbox.paypal.com/v2/payments/captures/".$transaction->transaction_id."/refund";
			//echo $url;exit;
			//$access_token = 'access_token$sandbox$msnfbrncgknr6hnw$ca1f469e8623f8616a42a4f759ff9867';
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

			$headers = array(
			   "Content-Type: application/json",
			   "Authorization: Bearer ".$access_token,
			   "PayPal-Request-Id: ".mktime(),
			);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

			$data = '{
			  "amount": {
				"value": "'.$booking->amount.'",
				"currency_code": "USD"
			  },
			  "invoice_id": "INVOICE-'.$booking->id.',
			  "note_to_payer": "Defective product"
			}';

			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

			//for debug only!
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

			$resp = curl_exec($curl);
			
			curl_close($curl);
			
			$obj = json_decode($resp);
			
			if (strtolower($obj->status) == 'completed') {
				$refunded = true;
				// refund transaction
				$query = $db->getQuery(true);
				$columns = array('room_id', 'booking_date', 'checkin_time', 'checkout_time', 'customer_name', 'customer_phone', 'customer_email', 'customer_address', 'booking_reason', 'admin_note', 'payment_gateway', 'amount', 'refund_trx_id');
					$values = array($db->Quote($booking->room_id), $db->Quote($booking->booking_date),  $db->Quote($booking->checkin_time),  $db->Quote($booking->checkout_time), $db->Quote($booking->customer_name), $db->Quote($booking->customer_phone), $db->Quote($booking->customer_email), $db->Quote($booking->customer_address), $db->Quote($booking->booking_reason), $db->Quote($booking->admin_note), $db->Quote($booking->payment_gateway), $db->Quote($booking->amount), $db->Quote($obj->id));
					$query
						->insert($db->quoteName('#__kirk_refunds'))
						->columns($db->quoteName($columns))
						->values(implode(',', $values));
					$db->setQuery($querybooking);
					$db->execute();
				}
			
		}
		
		if ($refunded) {
			$db->setQuery("DELETE FROM `#__kirk_bookings` WHERE `id` = '".$id."'");
			$db->Query();
			
			$db->setQuery("DELETE FROM `#__kirk_booking_events` WHERE `booking_id` = '".$id."'");
			$db->Query();
			
			$db->setQuery("DELETE FROM `#__kirk_booking_addons` WHERE `booking_id` = '".$id."'");
			$db->Query();
			
			$db->setQuery("DELETE FROM `#__kirk_booking_transactions` WHERE `booking_id` = '".$id."'");
			$db->Query();
		}
		
		$this->setRedirect('index.php?option=com_roombooking&view=bookings', 'Booking cancelled');
	}

 
}