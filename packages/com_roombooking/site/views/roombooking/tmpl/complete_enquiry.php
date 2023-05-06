<?php
defined( '_JEXEC' ) or die( '=;)' );
JHtml::_('behavior.formvalidator');
JHtml::_('formbehavior.chosen', 'select');
JHTML::_('behavior.calendar');

$app = JFactory::getApplication();
$input = $app->input;
$db = JFactory::getDBO();

//$db->setQuery("SELECT a.* , b.* FROM `#__kirk_bookings` AS a, `#__kirk_transactions` AS b WHERE a.`id` IN (".$input->getString('new_booking_id').") AND a.id = b.booking_id");
$db->setQuery("SELECT a.* FROM `#__kirk_booking_enquiries` AS a WHERE a.`id` IN (".$input->getString('enquiry_id').")");
$enquiry_details = $db->loadObjectList();


$params = JComponentHelper::getParams('com_roombooking');
?>
<h2>Booking Details</h2>
<div class="row roombooking">
	<div class="col-md-12">
		<table class="table table-striped">
			<tr>
				<th>Room</th>
				<th>Date</th>
				<th>From</th>
				<th>To</th>
				<th>Amount</th>
				<th>Payment Method</th>
			</tr>
			<?php
				$totalamt = 0;

				foreach ($enquiry_details as $enquiry_detail) {
					$customer_name = $enquiry_detail->customer_name;
					$customer_email = $enquiry_detail->customer_email;
					$customer_phone =  $enquiry_detail->customer_phone;
					$customer_address = $enquiry_detail->customer_address;
					$room_id = $enquiry_detail->room_id;
					$payment_gateway = $enquiry_detail->payment_gateway;
					$booking_enquiry_date = $enquiry_detail->booking_date;
					$checkin_time = $enquiry_detail->checkin_time;
					$checkin_time = str_replace('.', ':', $checkin_time);
					$checkout_time = $enquiry_detail->checkout_time;
					$checkout_time = str_replace('.', ':', $checkout_time);
					$amount = $enquiry_detail->amount;

					$totalamt+=$amount;

					$db->setQuery("SELECT * FROM `#__kirk_rooms` WHERE `id` = " . $room_id);
					$room_name = $db->loadObject()->room_name;
					?>
					<tr>
						<td><?php echo $room_name ; ?></td>
						<td><?php echo $booking_enquiry_date; ?></td>
						<td><?php echo date('h:i A', strtotime($checkin_time)); ?></td>
						<td><?php echo date('h:i A', strtotime($checkout_time)); ?></td>
						<td>$ <?php echo $amount; ?></td>
						<td><?php echo $payment_gateway; ?></td>
					</tr>

					<?php
				}
			?>
		</table>
</div>


