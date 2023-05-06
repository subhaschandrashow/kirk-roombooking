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

foreach($rooms as $r) {
	
	$db->setQuery("SELECT * FROM `#__kirk_bookings` WHERE `booking_date` = '".$booking_date."' AND `room_id` = '".$r->id."'");
	$bookingdetails = $db->loadObjectList();
	$timeslotarray = array();
	foreach($bookingdetails as $bd) {
		$checkin_time = $bd->checkin_time;
		$checkout_time = $bd->checkout_time;
		for($j=$checkin_time ; $j<$checkout_time ; $j++) {
			$timeslotarray[] = $j;
		}
	}
	$timeslotarray = array_unique($timeslotarray);
	$roomtimearray = array();
	for($k = 0 ; $k < 24 ; $k++) {
		$roomtimearray[$k] = 0;
	}
	foreach($timeslotarray as $ta) {
		$roomtimearray[$ta] = 1;
	}
	$roombookingdetails[$r->id] = $roomtimearray;
}

?>
<script type="text/javascript">
function fetchbookings() {
  jQuery('#fetchdetails').html('<div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>');
  jQuery.ajax({
	  method: "POST",
	  url: "index.php?option=com_roombooking&task=roombooking.ajaxbookingfetch",
	  data: { booking_date: jQuery('#booking_date').val() }
	})
	.done(function( html ) {
		jQuery('#fetchdetails').html(html);
	 });
}
</script>
<div class="row">
	<h1><?php echo JText::_('CHECKBOOKINGSTATUS'); ?></h1>
	<?php echo JHTML::_('calendar', date('Y-m-d'), 'booking_date', 'booking_date', '%Y-%m-%d'); ?>
	<input type="button" class="btn btn-secondary" value="<?php echo JText::_('FETCHBOOKING'); ?>" style="float: left;" onClick="fetchbookings();">
</div>
<div class="row roombooking" id="fetchdetails">
	<ul class="nav nav-tabs">
		<?php
		$i = 0;
		foreach($rooms as $r) {
		?>
		<li <?php if($i == 0) { ?>class="active"<?php } ?>><a data-toggle="tab" href="#room<?php echo $r->id ; ?>"><?php echo $r->room_name ; ?></a></li>
		<?php
		$i++;
		}
		?>
	</ul>
	<div class="tab-content">
  	  <?php
		$i = 0;
		foreach($rooms as $r) {
	  ?>
		  <div id="room<?php echo $r->id ; ?>" class="tab-pane fade <?php if($i == 0) { ?>in active<?php } ?>">
			<h3><?php echo JText::sprintf( 'ROOMBOOKINGDETAILS', $r->room_name , $booking_date) ; ?></h3>
			<div class="row">
			  <div class="col-sm-12">
				<div class="row">
			  	<?php
				$linesepcnt = 4 ;
				for($h=0; $h<=23; $h++) {
				$h4class = '';
				$labelclass = '';
				$statustxt = '';
				if($roombookingdetails[$r->id][$h] == 0) {
					$h4class = 'text-success';
					$labelclass = 'label-success';
					$statustxt = 'Available';
				}
				else {
					$h4class = 'text-danger';
					$labelclass = 'label-danger';
					$statustxt = 'Booked';
				}
				$endtime = $h+1 ; 
				if($endtime == 24) {
					$endtime = '23.59';
				}
				?>
				<div class="col-md-3">
					<div class="well">
					  <h4 class="<?php echo $h4class ; ?>"><span class="label <?php echo $labelclass ; ?> pull-right"><?php echo number_format($h , 2 , ':' , ':').'-'.number_format($endtime , 2 , ':' , ':') ; ?></span> <?php echo $statustxt ; ?> </h4>
					</div>
				</div>
				<?php
				
				}
				?>
				</div>
			  </div>  
			</div>
		  </div>
	  <?php
		$i++;
		}
	  ?>
	</div>
</div>
