<?php
defined( '_JEXEC' ) or die( '=;)' );
JHtml::_('behavior.formvalidator');
JHtml::_('formbehavior.chosen', 'select');
JHTML::_('behavior.calendar');
$app = JFactory::getApplication();

$db = JFactory::getDBO();
$params = JComponentHelper::getParams('com_roombooking');
$display_upto = $params->get('display_upto');
$displayrange = date('Y-m-d', strtotime("+".$display_upto." days"));

$db->setQuery("SELECT a.* , b.id as bid , b.room_id , b.booking_date , b.checkin_time , b.checkout_time , b.customer_name , b.customer_phone , b.customer_email , b.customer_address , b.booking_reason , b.admin_note , b.booking_master_id FROM `#__kirk_booking_events` as a , `#__kirk_bookings` as b WHERE ( a.booking_id = b.id OR a.booking_id = b.booking_master_id ) AND a.public = 1 AND b.`booking_date` > '".date('Y-m-d')."'  AND b.`booking_date` <= '".$displayrange."' ORDER BY b.booking_date , b.checkin_time");
$events = $db->loadObjectList();

?>

<div class="container upcoming_events">
<div class="row">
<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered table-hover">
	<?php
	foreach($events as $event) {
	if($event->booking_master_id == 0) {
		$eventid = $event->id ;
		$booking_id = 0;
	}
	else {
		$eventid = $event->booking_master_id ;
		$booking_id = $event->bid;
	}	
	$images = json_decode($event->images);
		
	$promotion_event_date_timestamp = strtotime($event->booking_date);
	$promotion_starting_date = strtotime("-".$event->promotion_starts." day", $promotion_event_date_timestamp);	
	if(date('Y-m-d') >= date('Y-m-d', $promotion_starting_date) && date('Y-m-d') < $event->booking_date) {		
	?>
	<tr>
		<td class="col-sm-3 center">
		<?php
		if(isset($images[0]) && $images[0] != '') {
		?>
		<a href="<?php echo JRoute::_('index.php?option=com_roombooking&view=eventdetails&event_id='.$eventid.'&booking_id='.$booking_id) ; ?>">
			<img src="<?php JURI::base() ; ?>administrator/components/com_roombooking/event_images/<?php echo $images[0] ; ?>">
		</a>
		<?php	
		}
		else {
		?>
		<a href="<?php echo JRoute::_('index.php?option=com_roombooking&view=eventdetails&event_id='.$eventid.'&booking_id='.$booking_id) ; ?>">
			<img src="<?php JURI::base() ; ?>images/Hall-front.jpeg">
		</a>
		<?php	
		}
		?>
		</td>
		<td>
			<h3><a href="<?php echo JRoute::_('index.php?option=com_roombooking&view=eventdetails&event_id='.$event->id.'&booking_id='.$booking_id) ; ?>"><?php echo $event->event_title ; ?></a></h3>
			<h4>Date: <?php echo date('l d/m/Y' , strtotime($event->booking_date)).' Time: '.$event->checkin_time.'-'.$event->checkout_time ; ?></h4>
		</td>
	</tr>
	<?php
	}
	}
	?>
</table>
</div>	
</div>
