<?php
defined('_JEXEC') or die;
$app = JFactory::getApplication();
$menu = $app->getMenu();
$active = $menu->getActive();
$itemId = $active->id;
$lang = JFactory::getLanguage();
$extension = 'com_roombooking';
$base_dir = JPATH_SITE;
$language_tag = 'en-GB';
$reload = true;
$lang->load($extension, $base_dir, $language_tag, $reload);
JHtml::_('behavior.formvalidator');
JHtml::_('formbehavior.chosen', 'select');
JHTML::_('behavior.calendar');

$document = JFactory::getDocument();
//$document->addStyleSheet(JURI::root().'components/com_vmapi/assets/css/bootstrap.css');
$document->addStyleSheet(JURI::root().'components/com_roombooking/assets/css/style.css');
$db = JFactory::getDBO();
$params = JComponentHelper::getParams('com_roombooking');

$main_local_price = $params->get('main_local_price');
$main_non_local_price = $params->get('main_non_local_price');
$main_business_price = $params->get('main_business_price');
$main_spin_cycle_price = $params->get('main_spin_cycle_price');
$meeting_local_price = $params->get('meeting_local_price');
$meeting_non_local_price = $params->get('meeting_non_local_price');
$meeting_business_price = $params->get('meeting_business_price');
$meeting_spin_cycle_price = $params->get('meeting_spin_cycle_price');
$full_building_local_price = $params->get('full_building_local_price');
$full_building_non_local_price = $params->get('full_building_non_local_price');
$full_building_business_price = $params->get('full_building_business_price');
$full_building_spin_cycle_price = $params->get('full_building_spin_cycle_price');

$db->setQuery("SELECT * FROM `#__kirk_rooms` WHERE `status` = 1");
$rooms = $db->loadObjectList();

// addons
$db->setQuery("SELECT * FROM `#__kirk_addons` WHERE `status` = 1");
$addons = $db->loadObjectList();

$roombookingdetails = array();
$booking_date = date('Y-m-d');
//$booking_date = '2019-05-07';
$addonstrids = '';
$addonarr = array();
foreach ($addons as $addon)
{
	$addonarr[] = 'input[name="addon_'.$addon->id.'"]';
}

if (count($addonarr) > 0)
{
	$addonstrids = ', '.implode(', ' , $addonarr);
}

// availability of search
/*$query = $db->getQuery(true);

$query->select('SUM(`checkout_time` - `checkin_time`) AS `btime`, `booking_date`');
$query->from('`#__kirk_bookings`');
$query->where('`booking_date` >= ' . $db->Quote(date('Y-m-d')));
$query->group('`booking_date`');
$query->order('`booking_date` ASC');
$query->having('`btime` < 24');

$db->setQuery($query);
$avbdaterecord = $db->loadObject();

$booksuggestiondate = '';
$booksuggestioncheckintime = '';
$booksuggestioncheckouttime = '';

if (isset($avbdaterecord))
{
	$booksuggestiondate = $avbdaterecord->booking_date;
}*/


?>
<script type="text/javascript">
      var onloadCallback = function() {
        grecaptcha.render('recaptcha_space', {
          'sitekey' : '<?php echo $params->get('sitekey'); ?>'
        });
      }
</script>

<script type="text/javascript">
function fetchbookings() {
  var todayTime = jQuery('#booking_date').val().split("/");
  var bookingdate = todayTime[2]+'-'+todayTime[1]+'-'+todayTime[0];
  var addtimerange = 0;
  jQuery('#fetchdetails').html('<div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>');
  if(jQuery('#addtimerange').attr('checked')) { addtimerange = 1 ; }
  jQuery.ajax({
	  method: "POST",
	  url: "index.php?option=com_roombooking&task=roombooking.ajaxbookingfetch",
	  data: { booking_date: bookingdate , addtimerange: addtimerange , date_range: jQuery('#date_range').val() }
	})
	.done(function( html ) {
		jQuery('#fetchdetails').html(html);
	 });
}

jQuery( document ).ready(function() {
	jQuery('#checkin_hour, #checkin_min, #checkout_hour, #checkout_min, #customer_type, #room_id <?php echo $addonstrids; ?>').change(function() {
	   // Do magical things

		var customer_type = jQuery('#customer_type').val();
		var number_of_rooms = 1;

		if (parseInt(customer_type) > 0)
		{
			var hourly_price = 0;

            if (jQuery('#room_id').val() == 0)
            {
                if (customer_type == 1) { hourly_price = '<?php echo $full_building_local_price; ?>'; }
                if (customer_type == 2) { hourly_price = '<?php echo $full_building_non_local_price; ?>'; }
                if (customer_type == 3) { hourly_price = '<?php echo $full_building_business_price; ?>'; }
                if (customer_type == 4) { hourly_price = '<?php echo $full_building_spin_cycle_price; ?>'; }
            }

            if (jQuery('#room_id').val() == 1)
            {
                if (customer_type == 1) { hourly_price = '<?php echo $main_local_price; ?>'; }
                if (customer_type == 2) { hourly_price = '<?php echo $main_non_local_price; ?>'; }
                if (customer_type == 3) { hourly_price = '<?php echo $main_business_price; ?>'; }
                if (customer_type == 4) { hourly_price = '<?php echo $main_spin_cycle_price; ?>'; }
            }

            if (jQuery('#room_id').val() == 2)
            {
                if (customer_type == 1) { hourly_price = '<?php echo $meeting_local_price; ?>'; }
                if (customer_type == 2) { hourly_price = '<?php echo $meeting_non_local_price; ?>'; }
                if (customer_type == 3) { hourly_price = '<?php echo $meeting_business_price; ?>'; }
                if (customer_type == 4) { hourly_price = '<?php echo $meeting_spin_cycle_price; ?>'; }
            }

			var from_hour = jQuery('#checkin_hour').val();
			var from_min = jQuery('#checkin_min').val();
			var to_hour = jQuery('#checkout_hour').val();
			var to_min = jQuery('#checkout_min').val();

			var start_min = parseInt(from_hour * 60) + parseInt(from_min);
			var stop_min = parseInt(to_hour * 60) + parseInt(to_min);
			var total_duration = stop_min - start_min;

			var total_price = 0;

			if (total_duration >= 0)
			{
				total_price = Math.ceil(total_duration / 60) * hourly_price;
			}

			// multiply by number of rooms
			total_price = number_of_rooms * total_price;

			<?php
			foreach ($addons as $addon)
			{
			?>
			if (jQuery('input[name="addon_<?php echo $addon->id; ?>"]:checked').val() == '1')
			{
				//alert(jQuery('input[name="addon_<?php echo $addon->id; ?>"]:checked').val());
				//alert(jQuery("#addon_<?php echo $addon->id; ?>_price").val());
				total_price = total_price + number_of_rooms * parseInt(jQuery("#addon_<?php echo $addon->id; ?>_price").val());
			}
			<?php
			}
			?>

			jQuery('#calculate_price').html('£ '+total_price);
			jQuery('#total_price').val(total_price);
		}
	})
})

jQuery(document).ready(function(){
    document.formvalidator.setHandler('matchemail', function(value) {
        return (jQuery('#customer_email').val() == value);
    });
});

function changeRoomType(value)
{
    var title = 4;
    if (value == 2)
    {
        jQuery("#customer_type").append('<option value="4">Spin Cycle</option>');
    }
    else
    {
        jQuery("#customer_type option[value=" + title + "]").remove();
    }

    jQuery("#customer_type").trigger("liszt:updated");

}

jQuery(document).ready(function() {
    jQuery('#customer_type').change(function () {
        // enable business name block if user type business/commerciall is selected
        jQuery('#business_name_block').hide();
        if (jQuery("#customer_type").val() == '3') {
            jQuery('#business_name_block').show();
        }
    })
})
</script>
<div class="container">
<div class="row">
	<h2><?php echo JText::_('CHECKBOOKINGSTATUS'); ?></h2>
	<div class="col-md-5 col-sm-12">
		<div class="col-md-7 col-sm-12">
			<?php echo JHTML::_('calendar', date('Y-m-d'), 'booking_date', 'booking_date', '%d/%m/%Y'); ?>
		</div>
		<div class="col-md-5 col-sm-12">
			<input type="button" class="btn btn-secondary" value="<?php echo JText::_('FETCHBOOKING'); ?>" style="float: left;" onClick="fetchbookings();">
		</div>
		<div style="clear: both;"></div>
	</div>
	<div class="col-md-7 col-sm-12">
        <div class="col-md-12 col-sm-12">
            <input type="checkbox" name="addtimerange" id="addtimerange" value="1"><?php echo JText::_('ADDATIMERANGE'); ?>
            <select name="date_range" id="date_range">
            <?php
                for($i = 1 ; $i <= 28; $i++) {
                    ?>
                    <option value="<?php echo $i ; ?>"><?php echo $i.' '.JText::_('Days'); ?></option>
                    <?php
                }
            ?>
            </select>
        </div>
	</div>
</div>
<div class="row roombooking" id="fetchdetails">
<h2><?php echo JText::_( 'ROOMBOOKING') ; ?></h2>
<div class="col-md-12">
	<div class="redblock">&nbsp;</div><div style="float: left; padding: 0px 5px 0 5px;">Booked</div><div class="greenblock">&nbsp;</div>&nbsp;<div style="float: left; padding: 0px 5px 0 5px;">Available</div><div class="yellowblock">&nbsp;</div>&nbsp;<div style="float: left; padding: 0px 5px 0 5px;">Provisionally Booked</div>
</div>
<table cellpadding="0" cellspacing="0" class="table-striped timetable">
<?php /*?><tr>
	<th width="250"></th>
	<th colspan="48" style="border-right: 1px solid #BBA9A9;">
		<h4 class="heading-1"><span>AM</span></h4>
	</th>
	<th colspan="48">
		<h4 class="heading-1"><span>PM</span></h4>
	</th>
</tr><?php */?>


<tr><th colspan="3"></th></tr>

<tr>
<th width="250"><?php echo JText::_('Time'); ?></th>
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
<tr>
	<th colspan="97" align="center" style="text-align: center;"><?php echo JText::sprintf( 'ROOMBOOKINGHEADING', date('l' , strtotime($booking_date)) , date('d/m/Y' , strtotime($booking_date))) ; ?></th>
</tr>
<?php
foreach($rooms as $room) {
$db->setQuery("SELECT * FROM `#__kirk_bookings` WHERE `booking_date` = '".$booking_date."' AND `room_id` = '".$room->id."'");
$bookingdetails = $db->loadObjectList();
$roomtimearray = array();
for($k = 0 ; $k < 96 ; $k++) {
	$roomtimearray[$k] = 0;
}

$db->setQuery("SELECT * FROM `#__kirk_booking_enquiries` WHERE `booking_date` = '".$booking_date."' AND `room_id` = '".$room->id."'");
$probookingdetails = $db->loadObjectList();
$proroomtimearray = array();
for($k = 0 ; $k < 96 ; $k++) {
	$proroomtimearray[$k] = 0;
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

$protimeslotarray = array();
foreach($probookingdetails as $bd) {
    $checkin_time = $bd->checkin_time;
    $c1 = explode('.' , $checkin_time);
    $c1index = $c1[0] * 4 + ($c1[1] / 15);
    $checkout_time = $bd->checkout_time;
    $c2 = explode('.' , $checkout_time);
    $c2index = $c2[0] * 4 + ($c2[1] / 15);
    for($j=$c1index ; $j<$c2index ; $j++) {
		$protimeslotarray[] = $j;
    }
	$protimeslotarray = array_unique($protimeslotarray);

    foreach($protimeslotarray as $ta) {
		$proroomtimearray[$ta] = 1;
    }
}

?>
<tr class="timebar">
<td width="250" class="roomname"><h5><?php echo $room->room_name ; ?></h5></td>
<?php
for($i = 0; $i <= 95 ; $i ++) {
$slotclass = '';
if ($roomtimearray[$i] == 1) {
	$slotclass = 'booked';
}
else
{
	if ($proroomtimearray[$i] == 1)
    {
		$slotclass = 'enquired';
	}
    else
    {
		$slotclass = 'notbooked';
	}
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
</table>
</div>


<div class="row roombookingform">
	<h2>Hirer’s Details:</h2>
	<form action="<?php echo JRoute::_('index.php?option=com_roombooking&task=roombooking.enquiry'); ?>" method="post" name="bookingform" id="bookingform" class="form-validate">

		<div class="col-md-6 col-sm-12">
		<div class="control-group">
			<div class="control-label">
				<label>Name <span class="required">*</span></label>
			</div>
			<div class="controls">
				<input id="customer_name" name="customer_name" type="text" value="" placeholder="Insert Your Name" required>
			</div>
		</div>

		<div class="control-group">
			<div class="control-label">
				<label>Email <span class="required">*</span></label>
			</div>
			<div class="controls">
				<input id="customer_email" name="customer_email" type="email" value="" placeholder="Insert Your Email Address" required>
			</div>
		</div>

        <div class="control-group">
            <div class="control-label">
                <label>Re Enter Email <span class="required">*</span></label>
            </div>
            <div class="controls">
                <input id="retype_customer_email" name="retype_customer_email" type="email" value="" placeholder="Re Enter Your Email Address" required class="validate-matchemail">
            </div>
        </div>

		</div>

		<div class="col-md-6 col-sm-12">
			<div class="control-group">
				<div class="control-label">
					<label>Telephone number <span class="required">*</span></label>
				</div>
				<div class="controls">
					<input id="customer_phone" name="customer_phone" type="tel" value="" placeholder="Telephone Number" required>
				</div>
			</div>

			<div class="control-group">
				<div class="control-label">
					<label>Address <span class="required">*</span></label>
				</div>
				<div class="controls">
					<textarea id="customer_address" name="customer_address" rows="40" placeholder="Address" required></textarea>
				</div>
			</div>
		</div>

		<h2>Date/Time of Booking</h2>

		<div class="col-md-6 col-sm-12">
			<div class="control-group">
				<div class="control-label">
					<label>Room</label>
				</div>
				<div class="controls">
				<select name="room_id" id="room_id" onchange="changeRoomType(this.value);">
				<option value="0">All Rooms</option>
				<?php
					$db->setQuery("SELECT * FROM `#__kirk_rooms`");
					$rooms = $db->loadObjectList();
					foreach($rooms as $room) {
					?>
					<option value="<?php echo $room->id ; ?>"><?php echo $room->room_name ; ?></option>
					<?php
					}
				?>
				</select>
				</div>
			</div>

			<div class="control-group">
				<div class="control-label">
					<label>Date <span class="required">*</span></label>
				</div>
				<div class="controls">
					<?php echo JHTML::_('calendar', date('Y-m-d'), 'booking_enquiry_date', 'booking_enquiry_date', '%d/%m/%Y'); ?>
				</div>
			</div>
		</div>

		<div class="col-md-6 col-sm-12">
			<div class="control-group">
				<div class="control-label">
					<label>From <span class="required">*</span></label>
				</div>

				<div class="controls">
					<?php
						$html="<select name='checkin_hour' id='checkin_hour' class='input-small'>";
						for ($h = 0; $h <= 23; $h++) {

							$html.="<option value='$h'>".sprintf("%02d", $h)."</option>";
						}
						$html.="</select>";
						$html.="<select name='checkin_min' id='checkin_min' class='input-small'>";
						$html.="<option value='00'>00</option>";
						$html.="<option value='15'>15</option>";
						$html.="<option value='30'>30</option>";
						$html.="<option value='45'>45</option>";
						$html.="</select>";
						echo $html;
					?>
				</div>
			</div>

			<div class="control-group">
				<div class="control-label">
					<label>To <span class="required">*</span></label>
				</div>
				<div class="controls">
					<?php
						$html="<select name='checkout_hour' id='checkout_hour' class='input-small'>";
						for ($h = 0; $h <= 23; $h++) {

							$html.="<option value='$h'>".sprintf("%02d", $h)."</option>";
						}
						$html.="</select>";
						$html.="<select name='checkout_min' id='checkout_min' class='input-small'>";
						$html.="<option value='00'>00</option>";
						$html.="<option value='15'>15</option>";
						$html.="<option value='30'>30</option>";
						$html.="<option value='45'>45</option>";
						$html.="</select>";
						echo $html;
					?>
				</div>
			</div>
		</div>

		<div class="col-md-12 col-sm-12">
			<p><strong>Please allow time at beginning/end of events for preparation, clearing up and cleaning. 15 minutes at either end of your hiring is included in your costs.</strong></p>
		</div>

		<div class="col-md-6 col-sm-12">
			<div class="control-group">
				<div class="control-label">
					<label>Reason for booking <span class="required">*</span></label>
				</div>
				<div class="controls">
					<textarea name="booking_reason" id="booking_reason" placeholder="Reason for Booking" required class="inputbox"></textarea>
				</div>
			</div>

            <div class="control-group">
                <div class="control-label">
                    <label>Additional information</label>
                </div>
                <div class="controls">
                    <textarea name="add_info" id="add_info" placeholder="i.e frequency and dates of bookings required ( if more than a single date booking)" class="inputbox" rows="10"></textarea>
                </div>
            </div>
		</div>

		<div class="col-md-6 col-sm-12">
		<?php
			foreach ($addons as $addon)
			{
			?>
			<div class="control-group">
				<div class="control-label">
					<span><?php echo $addon->addon_description; ?></span>
				</div>
				<div class="controls">
					<div>
						<div class="item-radio-label-inline"><label><input type="radio" name="addon_<?php echo $addon->id; ?>" value="1">Yes</label></div>
						<div class="item-radio-label-inline"><label><input type="radio" name="addon_<?php echo $addon->id; ?>" value="0" checked>No</label></div>
						<input type="hidden" name="addon_<?php echo $addon->id; ?>_price" id="addon_<?php echo $addon->id; ?>_price" value="<?php echo $addon->price; ?>">
					</div>
				</div>
			</div>
			<?php
			}
		?>

			<div class="control-group">
				<div class="control-label">
					<span>Is this an event you wish us to publicise ?</span>
				</div>
				<div class="controls">
					<div>
						<div class="item-radio-label-inline"><label><input type="radio" name="event_required" value="1">Yes</label></div>
						<div class="item-radio-label-inline"><label><input type="radio" name="event_required" value="0" checked>No</label></div>
					</div>
				</div>
			</div>

		</div>


		<div class="col-md-6 col-sm-12">
			<div class="control-group">
				<div class="control-label">
					<span>User Type</span>
				</div>
				<div class="controls">
					<select name="customer_type" id="customer_type">
						<option value="1">Local Resident</option>
						<option value="2">Non Local Resident</option>
						<option value="3">Business/Commercial</option>
					</select>
				</div>
			</div>

            <div class="control-group" id="business_name_block" style="display: none;">
                <div class="control-label">
                    <span>Business name</span>
                </div>
                <div class="controls">
                    <input type="text" name="business_name">
                </div>
            </div>


		</div>

		<div class="col-md-6 col-sm-12" id="pricediv">
			<div class="control-group">
				<div class="control-label">
					<label>Price</label>
				</div>
				<div class="controls">
					<span id="calculate_price">£ 0.00</span>
					<input type="hidden" id="total_price" name="total_price">
				</div>
			</div>
		</div>

		<!--<div class="control-group" align="center">
			<div class="controls">
				<select name="payment_gateway">
					<option value="paypal">Paypal</option>
					<option value="stripe">Stripe</option>
				</select>
			</div>
		</div>-->

        <div class="control-group" align="center">
            <div class="controls">
                <input type="checkbox" name="tnc" required>&nbsp;Read & accept terms of hire  <a href="<?php echo JURI::base().'images/KVHConditions-of-Hire-Nov-21.pdf'; ?>" target="_blank">Click Here</a>
            </div>
        </div>

        <div class="control-group" align="center">
            <div class="controls" style="color: red;">
                Invoice will be sent and booking confirmed on payment
            </div>
        </div>

		<div class="col-md-12 col-sm-12" align="center" style="margin-top: 20px;">
			<div id="recaptcha_space"></div>
			<script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>
		</div>

        <div class="col-md-12 col-sm-12" align="center" style="margin-top: 20px;">
            <button type="submit" class="btn btn-primary submit-button"><span class="caption">Book</span></button>
        </div>

		<input type="hidden" name="Itemid" value="<?php echo $itemId ; ?>">
	</form>
</div>
</div>
