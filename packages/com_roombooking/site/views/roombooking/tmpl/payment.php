<?php
defined( '_JEXEC' ) or die( '=;)' );
JHtml::_('behavior.formvalidator');
JHtml::_('formbehavior.chosen', 'select');
JHTML::_('behavior.calendar');

$app = JFactory::getApplication();
$input = $app->input;

$db = JFactory::getDBO();
$params = JComponentHelper::getParams('com_roombooking');



$enquiry_id_arr = explode(',', $input->getString('enquiry_id'));
$enquiry_id = $enquiry_id_arr[0];

$db->setQuery("SELECT * FROM `#__kirk_booking_enquiries` WHERE `id` IN (".$input->getString('enquiry_id').")");
$enquiry_details = $db->loadObjectList();

$payment_gateway = $input->getString('payment_gateway');

$pg_name = '';
if ($payment_gateway == 'paypal') { $pg_name = 'Paypal'; }
else if ($payment_gateway == 'stripe') { $pg_name = 'Stripe'; }
else if ($payment_gateway == 'pay_later') { $pg_name = 'Pay Latr'; }



//$booking_date = '2019-05-07';
?>
<script type="text/javascript">

</script>

<div class="row roombooking">
<!--<h2>Pay via <?php /*echo $pg_name ; */?></h2>-->

<?php
if ($payment_gateway == 'paypal') {
$mode = $params->get('paypal_mode');
$paypal_email = $params->get('paypal_email');
	
if($mode == 0) {
	$paypal_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
}
else {
	$paypal_url = 'https://www.paypal.com/cgi-bin/webscr';
}
?>
<form action="<?php echo $paypal_url ; ?>" method="post" target="_top">
	<div class="col-md-12">
		<table class="table table-striped">
			<tr>
				<th>Room</th>
				<th>Date</th>
				<th>From</th>
				<th>To</th>
				<th>Email</th>
				<th>Amount</th>
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
						<td><?php echo $customer_email; ?></td>
						<td>$ <?php echo $amount; ?></td>
					</tr>

					<?php
				}
			?>
			<tr>
				<td colspan="5" align="right">Total to Pay</td>
				<td>
					<?php echo '$ '.$totalamt; ?>
				</td>
			</tr>
			
			<tr>
				<td colspan="5"></td>
				<td>
					<input type="submit" name="pay_now" id="pay_now" value="<?php echo JText::_('Pay Now'); ?>" class="btn btn-primary">
				</td>
			</tr>
		</table>
	</div>
	<input type="hidden" name="business" value="<?php echo $paypal_email ; ?>">
	<input type="hidden" name="item_name" value="Room Booking/<?php echo $booking_enquiry_date;?>">
    <input type="hidden" name="item_number" value="RBPAY#<?php echo $enquiry_id; ?>">
	<input type="hidden" name="amount" value="<?php echo $totalamt; ?>">
    <input type="hidden" name="no_shipping" value="1">
    <input type="hidden" name="currency_code" value="USD">
    <input type="hidden" name="notify_url" value="<?php echo JURI::base().'index.php?option=com_roombooking&task=paypalnotify'; ?>">
    <input type="hidden" name="cancel_return" value="<?php echo JURI::base().'index.php?option=com_roombooking&task=paypalcancel'; ?>">
    <input type="hidden" name="return" value="<?php echo JURI::base().'index.php?option=com_roombooking&task=paypalreturn'; ?>">
    <input type="hidden" name="custom" value="enquiry_id=<?php echo $input->getString('enquiry_id'); ?>">
    <input type="hidden" name="cmd" value="_xclick">
</form>

<?php
}
if ($payment_gateway == 'stripe') {
$stripe_mode = $params->get('stripe_mode');
$secret_key = $params->get('stripe_secret_key');
$publish_key = $params->get('stripe_publish_key');
?>
	<style type="text/css">
		body { font-family: Arial; width:100%; margin: 0 auto; } #frmStripePayment {  max-width: 300px; padding: 25px; border: #D0D0D0 1px solid; border-radius: 4px; margin: 0 auto; }.test-data { margin-top: 40px; } #frmStripePayment .field-row {  margin-bottom: 20px; } #frmStripePayment div label {  margin: 5px 0px 0px 5px; color: #49615d; width: auto; } .demoInputBox { padding: 10px; border: #d0d0d0 1px solid; border-radius: 4px; background-color: #FFF; width: 100%; margin-top: 5px; box-sizing:border-box; } .demoSelectBox { padding: 10px; border: #d0d0d0 1px solid; border-radius: 4px; background-color: #FFF; margin-top: 5px; } select.demoSelectBox { height: 35px; margin-right: 10px; } .error {  background-color: #FF6600; padding: 8px 10px; border-radius: 4px; font-size: 0.9em; } .success { background-color: #c3c791; padding: 8px 10px; border-radius: 4px; font-size: 0.9em; } .info { font-size: .8em; color: #FF6600; letter-spacing: 2px; padding-left: 5px; } .btnAction { background-color: #586ada; padding: 10px 40px; color: #FFF; border: #5263cc 1px solid; border-radius: 4px; cursor:pointer; } .btnAction:focus { outline: none; } .column-right { margin-right: 6px; } .contact-row { display: inline-block; } .cvv-input { width: 60px; } #error-message {    margin: 0px 0px 10px 0px; padding: 5px 25px; border-radius: 4px; line-height: 25px; font-size: 0.9em; color: #ca3e3e; border: #ca3e3e 1px solid; display: none; width: 300px; } #success-message { margin: 0px 0px 10px 0px; padding: 5px 25px; border-radius: 4px; line-height: 25px; font-size: 0.9em; color: #3da55d; border: #43b567 1px solid; width: 300px; } .display-none { display:none; } #response-container { padding: 40px 20px; width: 270px; text-align:center; } .ack-message { font-size: 1.5em; margin-bottom: 20px; } #response-container.success { border-top: #b0dad3 2px solid; background: #e9fdfa; }#response-container.error { border-top: #c3b4b4 2px solid; background: #f5e3e3; } .img-response { margin-bottom: 30px;  } #loader { display: none; } #loader img { width: 45px; vertical-align: middle; }
	</style>
	<table class="table table-striped">
		<tr>
			<th>Room</th>
			<th>Date</th>
			<th>From</th>
			<th>To</th>
			<th>Email</th>
			<th>Amount</th>
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
						<td><?php echo $customer_email; ?></td>
						<td>$ <?php echo $amount; ?></td>
					</tr>

					<?php
				}
			?>
			<tr>
				<td colspan="5" align="right">Total to Pay</td>
				<td>
					<?php echo '$ '.$totalamt; ?>
				</td>
			</tr>
	</table>
	<div id="error-message" align="center" style="margin: 0 auto;"></div>
	<form action="<?php echo JRoute::_('index.php?option=com_roombooking&task=stripeprocessing');?>" method="post" target="_top" id="frmStripePayment_id">
		<div class="col-md-12">
			<table class="table table-striped">
				<tr>
					<td><label>Card Holder Name</label>
						<span id="card-holder-name-info" class="info"></span>
					</td>
					<td><input type="text" id="name" name="name"
                        class="demoInputBox"></td>
				</tr>
				
				<tr>
					<td><label>Email</label> <span id="email-info"
                        class="info"></span>
					</td>
					<td><input type="text"
                        id="email" name="email" class="demoInputBox"></td>
				</tr>
				
				<tr>
					<td><label>Card Number</label> <span
                        id="card-number-info" class="info"></span>
					</td>
					<td><input
                        type="text" id="card-number" name="card-number"
                        class="demoInputBox">
                    </td>
				</tr>
				
				<tr>
					<td><label>Expiry Month / Year</label> <span
                            id="userEmail-info" class="info"></span>
					</td>
					<td>
                          <table>
                          	<tr>
                          		<td>
                          			<select name="month" id="month"
										class="demoSelectBox select">
										<option value="08">08</option>
										<option value="09">9</option>
										<option value="10">10</option>
										<option value="11">11</option>
										<option value="12">12</option>
									</select>
                          		</td>
                          		<td>
                          			<select name="year" id="year"
										class="demoSelectBox">
										<option value="18">2018</option>
										<option value="19">2019</option>
										<option value="20">2020</option>
										<option value="21">2021</option>
										<option value="22">2022</option>
										<option value="23">2023</option>
										<option value="24">2024</option>
										<option value="25">2025</option>
										<option value="26">2026</option>
										<option value="27">2027</option>
										<option value="28">2028</option>
										<option value="29">2029</option>
										<option value="30">2030</option>
									</select>
                          		</td>
                          	</tr>
                          </table>
                            
                    </td>
				</tr>
				
				<tr>
					<td><label>CVC</label> <span id="cvv-info"
                            class="info"></span>
					</td>
					<td><input type="text"
                            name="cvc" id="cvc"
                            class="demoInputBox cvv-input input-small">
                    </td>
				</tr>
				
				<tr>
					<td colspan="2">
						<input type="submit" name="pay_now" value="Pay via Stripe" id="submit-btn" class="btnAction" onClick="stripePay(event);">
						<div id="loader">
							<img alt="loader" src="<?php echo JURI::base();?>components/com_roombooking/library/LoaderIcon.gif">
						</div>
					</td>
				</tr>
			</table>
		</div>
		<input type="hidden" name="amount" value="<?php echo $totalamt; ?>">
		<input type="hidden" name="currency_code" value="USD">
		<input type="hidden" name="item_name" value="Room Booking/<?php echo $room_name;?>/<?php echo $booking_enquiry_date;?>">
        <input type="hidden" name="item_number" value="RBPAY#<?php echo $input->getString('enquiry_id'); ?>">
        <input type="hidden" name="enquiry_id" value="<?php echo $input->getString('enquiry_id'); ?>">
	</form>
	<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
	<script>
				function cardValidation () {
					var valid = true;
					var name = jQuery('#name').val();
					var email = jQuery('#email').val();
					var cardNumber = jQuery('#card-number').val();
					var month = jQuery('#month').val();
					var year = jQuery('#year').val();
					var cvc = jQuery('#cvc').val();

					jQuery("#error-message").html("").hide();

					if (name.trim() == "") {
						valid = false;
					}
					if (email.trim() == "") {
						   valid = false;
					}
					if (cardNumber.trim() == "") {
						   valid = false;
					}

					if (month.trim() == "") {
							valid = false;
					}
					if (year.trim() == "") {
						valid = false;
					}
					if (cvc.trim() == "") {
						valid = false;
					}

					if(valid == false) {
						jQuery("#error-message").html("<?php echo JText::_('All fields are required');?>").show();
					}

					return valid;
				}
				
				//set your publishable key
				Stripe.setPublishableKey("<?php echo $publish_key; ?>");

				//callback to handle the response from stripe
				function stripeResponseHandler(status, response) {
					if (response.error) {
						//enable the submit button
						jQuery("#submit-btn").show();
						jQuery( "#loader" ).css("display", "none");
						//display the errors on the form
						jQuery("#error-message").html(response.error.message).show();
					} else {
						//get token id
						var token = response['id'];
						//insert the token into the form
						jQuery("#frmStripePayment_id").append("<input type='hidden' name='token' value='" + token + "' />");
						//submit form to the server
						jQuery("#frmStripePayment_id").submit();
					}
				}
				function stripePay(e) {
					e.preventDefault();
					var valid = cardValidation();

					if(valid == true) {
						jQuery("#submit-btn").hide();
						jQuery( "#loader" ).css("display", "inline-block");
						Stripe.createToken({
							number: jQuery('#card-number').val(),
							cvc: jQuery('#cvc').val(),
							exp_month: jQuery('#month').val(),
							exp_year: jQuery('#year').val()
						}, stripeResponseHandler);

						//submit from callback
						return false;
					}
				}
			</script>
<?php	
}

if  ($payment_gateway == 'pay_later')
{
    $pay_later_url = JRoute::_('index.php?option=com_roombooking&task=pay_later_process')
?>
    <form action="<?php echo $pay_later_url ; ?>" method="post" target="_top">
        <div class="col-md-12">
            <table class="table table-striped">
                <tr>
                    <th>Room</th>
                    <th>Date</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Email</th>
                    <th>Amount</th>
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
                        <td><?php echo $customer_email; ?></td>
                        <td>$ <?php echo $amount; ?></td>
                    </tr>

					<?php
				}
				?>
                <tr>
                    <td colspan="5" align="right">Total to Pay</td>
                    <td>
						<?php echo '$ '.$totalamt; ?>
                    </td>
                </tr>

                <tr>
                    <td colspan="5"></td>
                    <td>
                        <input type="submit" name="pay_now" id="pay_now" value="<?php echo JText::_('Book Now'); ?>" class="btn btn-primary">
                    </td>
                </tr>
            </table>
        </div>

        <input type="hidden" name="amount" value="<?php echo $totalamt; ?>">
        <input type="hidden" name="enquiry_id" value="<?php echo $input->getString('enquiry_id'); ?>">
    </form>
<?php
}
?>
</div>
