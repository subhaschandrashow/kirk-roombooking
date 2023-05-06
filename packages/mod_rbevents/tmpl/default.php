<?php
defined('_JEXEC') or die();

$promotion_starts = $params->get('promotion_starts');
$db = JFactory::getDBO();
$promotionrange = date('Y-m-d', strtotime("+".$promotion_starts." days"));

$db->setQuery("SELECT a.* , b.id as bid , b.room_id , b.booking_date , b.checkin_time , b.checkout_time , b.customer_name , b.customer_phone , b.customer_email , b.customer_address , b.booking_reason , b.admin_note , b.booking_master_id  FROM `#__kirk_booking_events` as a , `#__kirk_bookings` as b WHERE ( a.booking_id = b.id OR a.booking_id = b.booking_master_id ) AND b.`booking_date` >= '".date('Y-m-d')."' AND b.`booking_date` <= '".$promotionrange."' AND a.public = 1 ORDER BY b.booking_date , b.checkin_time");
$events = $db->loadObjectList();
?>
<table class="mod_events_latest_table jevbootstrap" width="100%" cellpadding="0" cellspacing="0">
	<tbody>
	<?php
	foreach($events as $event) {
	if($event->booking_master_id == 0) {
		$eventid = $event->id ;
		$booking_id = 0;
	}
	else {
		$eventid = $event->id ;
		$booking_id = $event->bid;
	}
	$eventid = $event->id ;
	$promotion_event_date_timestamp = strtotime($event->booking_date);
	$promotion_starting_date = strtotime("-".$event->promotion_starts." day", $promotion_event_date_timestamp);
	if(date('Y-m-d') >= date('Y-m-d', $promotion_starting_date) && date('Y-m-d') <= $event->booking_date) {
	?>
	<tr>
		<td class="mod_events_latest_first" style="border-color:#ccc">
			<span class="icon-calendar"></span>
			<span class="mod_events_latest_date"><?php echo date('d F Y' , strtotime($event->booking_date)) ; ?></span>
			<br>
			<span class="icon-time"></span>
			<span class="mod_events_latest_date"><?php echo $event->checkin_time; //echo date('h:i A' , $event->dtstart) ; ?></span> - <span class="mod_events_latest_date"><?php echo $event->checkout_time ; //echo date('h:i A' , $event->dtend) ; ?></span>
			<br>
			<span class="icon-hand-right"></span>
			<strong><span class="mod_events_latest_content"><a href="<?php echo JRoute::_('index.php?option=com_roombooking&view=eventdetails&event_id='.$eventid.'&booking_id='.$booking_id) ; ?>" target="_top"><?php echo $event->event_title ; ?></a></span></strong>
		</td>
	</tr>
	<?php
	}
	}
	?>
	</tbody>
</table>
