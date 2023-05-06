<?php
defined( '_JEXEC' ) or die( '=;)' );
JHtml::_('behavior.formvalidator');
JHtml::_('formbehavior.chosen', 'select');
JHTML::_('behavior.calendar');
$app = JFactory::getApplication();
$input = $app->input;
$event_id = $input->get('event_id');
$booking_id = $input->get('booking_id');
$db = JFactory::getDBO();
$params = JComponentHelper::getParams('com_roombooking');
$db->setQuery("SELECT * FROM `#__kirk_booking_events` WHERE id = ".$event_id);
$event = $db->loadObject();

if($booking_id == 0)
{
	$booking_id = $event->booking_id;
}
$db->setQuery("SELECT * FROM `#__kirk_bookings` WHERE id = ".$booking_id);
$booking = $db->loadObject();
?>
<script type="text/javascript">
function changeimage(imgs) {
  var expandImg = document.getElementById("expandedImg");
  var imgText = document.getElementById("imgtext");
  expandImg.src = imgs.src;
  imgText.innerHTML = imgs.alt;
  expandImg.parentElement.style.display = "block";
}
</script>
<div class="container">
	<h1><?php echo $event->event_title ; ?></h1>
	<h2>Date: <?php echo date('l d/m/Y' , strtotime($booking->booking_date)).' Time: '.$booking->checkin_time.'-'.$booking->checkout_time ; ?></h2>
	<div class="row">
		<div class="col-md-6 col-sm-12">
		<?php
		$images = json_decode($event->images);
		?>	
		<div class="row px-2">
		 
		<?php
			if(isset($images[0]) && $images[0] != '') {
		?>
		  <!-- Expanded image -->
		  <img id="expandedImg" style="width:100%" src="<?php JURI::base() ; ?>administrator/components/com_roombooking/event_images/<?php echo $images[0] ; ?>">
		<?php	
			}
		?>
		  <!-- Image text -->
		  <div id="imgtext"></div>
		</div>
		<div class="row px-2">
	    <?php
			if(isset($images[0]) && $images[0] != '') {
			?>
			<div class="column">
				<img src="<?php JURI::base() ; ?>administrator/components/com_roombooking/event_images/<?php echo $images[0] ; ?>" alt="" onclick="changeimage(this);">
			  </div>
			<?php	
			}
			if(isset($images[1]) && $images[1] != '') {
			?>
			<div class="column">
				<img src="<?php JURI::base() ; ?>administrator/components/com_roombooking/event_images/<?php echo $images[1] ; ?>" alt="" onclick="changeimage(this);">
			  </div>
			<?php	
			}
			if(isset($images[2]) && $images[2] != '') {
			?>
			<div class="column">
				<img src="<?php JURI::base() ; ?>administrator/components/com_roombooking/event_images/<?php echo $images[2] ; ?>" alt="" onclick="changeimage(this);">
			  </div>
			<?php	
			}
			if(isset($images[3]) && $images[3] != '') {
			?>
			<div class="column">
				<img src="<?php JURI::base() ; ?>administrator/components/com_roombooking/event_images/<?php echo $images[3] ; ?>" alt="" onclick="changeimage(this);">
			  </div>
			<?php	
			}
			if(isset($images[4]) && $images[4] != '') {
			?>
			<div class="column">
				<img src="<?php JURI::base() ; ?>administrator/components/com_roombooking/event_images/<?php echo $images[4] ; ?>" alt="" onclick="changeimage(this);">
			  </div>
			<?php	
			}
		?>
		  
		  
		</div>
		</div>
		<div class="col-md-6 col-sm-12">
			<div class="px-2">
				<?php echo $event->event_description ; ?>
			</div>
		</div>
	</div>
	
	
</div>
