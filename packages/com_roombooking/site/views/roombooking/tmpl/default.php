<?php
defined( '_JEXEC' ) or die( '=;)' );
JHtml::_('behavior.formvalidator');
JHtml::_('formbehavior.chosen', 'select');
JHTML::_('behavior.calendar');
$db = JFactory::getDBO();
$params = JComponentHelper::getParams('com_roombooking');
$db->setQuery("SELECT * FROM `#__kirk_rooms` WHERE `status` = 1");
$rooms = $db->loadObjectList();

$roombookingdetails = array();
$booking_date = date('Y-m-d');
//$booking_date = '2019-05-07';
?>
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
</script>
<div class="row">
	<h1><?php echo JText::_('CHECKBOOKINGSTATUS'); ?></h1>
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
<h3><?php echo JText::_( 'ROOMBOOKING') ; ?></h3>
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

<tr>
	<th colspan="3"></th>
</tr>
	
	
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
?>
<tr class="timebar">
<td width="250" class="roomname"><h5><?php echo $room->room_name ; ?></h5></td>
<?php
for($i = 0; $i <= 95 ; $i ++) {
$slotclass = '';
if($roomtimearray[$i] == 1) {
	$slotclass = 'booked';
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
</table>
</div>
